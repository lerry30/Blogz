#!/usr/bin/env python3
"""
Codebase Search Tool
A fast and flexible tool to search for specific words or patterns in your entire codebase.
"""

import os
import re
import argparse
import sys
from pathlib import Path
from typing import List, Tuple, Set
import mimetypes

class CodebaseSearcher:
    def __init__(self):
        # Common code file extensions
        self.code_extensions = {
            '.py', '.js', '.ts', '.jsx', '.tsx', '.java', '.c', '.cpp', '.h', '.hpp',
            '.cs', '.php', '.rb', '.go', '.rs', '.swift', '.kt', '.scala', '.r',
            '.sql', '.html', '.htm', '.css', '.scss', '.sass', '.less', '.xml',
            '.json', '.yaml', '.yml', '.toml', '.ini', '.cfg', '.conf', '.sh',
            '.bash', '.zsh', '.fish', '.ps1', '.bat', '.cmd', '.dockerfile',
            '.md', '.rst', '.txt', '.log', '.gitignore', '.env'
        }

        # Directories to ignore by default
        self.ignore_dirs = {
            '.git', '.svn', '.hg', '__pycache__', 'node_modules', '.pytest_cache',
            '.tox', 'venv', 'env', '.env', 'virtualenv', '.venv', 'build', 'dist',
            '.build', '.dist', 'target', 'bin', 'obj', '.idea', '.vscode', '.vs',
            'coverage', '.coverage', '.nyc_output', 'logs', 'tmp', 'temp', '.tmp'
        }

        # Binary file extensions to skip
        self.binary_extensions = {
            '.exe', '.dll', '.so', '.dylib', '.a', '.lib', '.bin', '.dat',
            '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.ico', '.tiff',
            '.mp3', '.mp4', '.avi', '.mov', '.wmv', '.flv', '.wav', '.ogg',
            '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
            '.zip', '.tar', '.gz', '.bz2', '.7z', '.rar', '.deb', '.rpm'
        }

    def is_text_file(self, file_path: Path) -> bool:
        """Check if a file is likely to be a text file."""
        if file_path.suffix.lower() in self.binary_extensions:
            return False

        if file_path.suffix.lower() in self.code_extensions:
            return True

        # Use mimetypes to guess
        mime_type, _ = mimetypes.guess_type(str(file_path))
        if mime_type and mime_type.startswith('text'):
            return True

        # For files without extension or unknown types, try to read first few bytes
        try:
            with open(file_path, 'rb') as f:
                chunk = f.read(1024)
                if b'\0' in chunk:  # Binary files often contain null bytes
                    return False
                # Check if it's mostly printable ASCII
                try:
                    chunk.decode('utf-8')
                    return True
                except UnicodeDecodeError:
                    return False
        except (OSError, IOError):
            return False

    def should_ignore_dir(self, dir_name: str, custom_ignore: Set[str] = None) -> bool:
        """Check if a directory should be ignored."""
        ignore_set = self.ignore_dirs
        if custom_ignore:
            ignore_set = ignore_set.union(custom_ignore)
        return dir_name in ignore_set or dir_name.startswith('.')

    def search_in_file(self, file_path: Path, pattern: str, case_sensitive: bool = False, 
                      regex: bool = False, whole_word: bool = False) -> List[Tuple[int, str]]:
        """Search for pattern in a single file and return matches with line numbers."""
        matches = []

        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                for line_num, line in enumerate(f, 1):
                    line_content = line.rstrip('\n\r')

                    if regex:
                        flags = 0 if case_sensitive else re.IGNORECASE
                        if whole_word:
                            pattern = r'\b' + pattern + r'\b'
                        try:
                            if re.search(pattern, line_content, flags):
                                matches.append((line_num, line_content))
                        except re.error as e:
                            print(f"Regex error in pattern '{pattern}': {e}", file=sys.stderr)
                            return []
                    else:
                        search_line = line_content if case_sensitive else line_content.lower()
                        search_pattern = pattern if case_sensitive else pattern.lower()

                        if whole_word:
                            # Simple whole word matching for non-regex
                            words = re.findall(r'\b\w+\b', search_line)
                            if search_pattern in words:
                                matches.append((line_num, line_content))
                        else:
                            if search_pattern in search_line:
                                matches.append((line_num, line_content))

        except (OSError, IOError, UnicodeDecodeError) as e:
            print(f"Error reading {file_path}: {e}", file=sys.stderr)

        return matches

    def search_codebase(self, root_dir: str, pattern: str, file_pattern: str = None,
                       case_sensitive: bool = False, regex: bool = False, 
                       whole_word: bool = False, max_results: int = None,
                       include_dirs: List[str] = None, exclude_dirs: List[str] = None) -> None:
        """Search for pattern in the entire codebase."""
        root_path = Path(root_dir).resolve()

        if not root_path.exists():
            print(f"Error: Directory '{root_dir}' does not exist.", file=sys.stderr)
            return

        print(f"Searching for: '{pattern}' in {root_path}")
        print(f"Options: case_sensitive={case_sensitive}, regex={regex}, whole_word={whole_word}")
        print("-" * 80)

        total_files = 0
        matching_files = 0
        total_matches = 0

        # Convert exclude_dirs to set for faster lookup
        custom_ignore = set(exclude_dirs) if exclude_dirs else set()

        for root, dirs, files in os.walk(root_path):
            # Filter out ignored directories
            dirs[:] = [d for d in dirs if not self.should_ignore_dir(d, custom_ignore)]

            # If include_dirs is specified, only search in those directories
            if include_dirs:
                current_rel_path = os.path.relpath(root, root_path)
                if not any(current_rel_path.startswith(inc_dir) or inc_dir in current_rel_path 
                          for inc_dir in include_dirs):
                    continue

            for file in files:
                file_path = Path(root) / file

                # Skip if file doesn't match file pattern
                if file_pattern and not re.search(file_pattern, file):
                    continue
                    
                # Skip binary files
                if not self.is_text_file(file_path):
                    continue
                    
                total_files += 1
                matches = self.search_in_file(file_path, pattern, case_sensitive, regex, whole_word)
                
                if matches:
                    matching_files += 1
                    rel_path = file_path.relative_to(root_path)
                    print(f"\n📁 {rel_path} ({len(matches)} matches):")
                    
                    for line_num, line_content in matches:
                        # Highlight the matched pattern
                        display_line = line_content
                        if not regex:  # Simple highlighting for non-regex searches
                            search_pattern = pattern if case_sensitive else pattern
                            if not case_sensitive:
                                # Case-insensitive highlighting
                                def replace_func(match):
                                    return f"🔍[{match.group()}]"
                                display_line = re.sub(re.escape(search_pattern), replace_func, 
                                                    display_line, flags=re.IGNORECASE)
                            else:
                                display_line = display_line.replace(pattern, f"🔍[{pattern}]")
                        
                        print(f"  {line_num:4d}: {display_line}")
                        total_matches += 1
                        
                        # Check max results limit
                        if max_results and total_matches >= max_results:
                            print(f"\n⚠️  Reached maximum results limit ({max_results})")
                            break
                    
                    if max_results and total_matches >= max_results:
                        break
                        
            if max_results and total_matches >= max_results:
                break
        
        print("\n" + "=" * 80)
        print(f"📊 Search Summary:")
        print(f"   Files searched: {total_files}")
        print(f"   Files with matches: {matching_files}")
        print(f"   Total matches: {total_matches}")

def main():
    parser = argparse.ArgumentParser(
        description="Search for specific words or patterns in your codebase",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  %(prog)s "function" .                    # Search for 'function' in current directory
  %(prog)s "TODO|FIXME" . -r              # Search for TODO or FIXME using regex
  %(prog)s "class" . -f "\.py$"           # Search only in Python files
  %(prog)s "config" . -c                  # Case-sensitive search
  %(prog)s "var" . -w                     # Whole word search
  %(prog)s "error" . -m 50                # Limit to 50 matches
  %(prog)s "debug" . --exclude node_modules dist  # Exclude specific directories
        """
    )
    
    parser.add_argument("pattern", help="Pattern to search for")
    parser.add_argument("directory", nargs="?", default=".", 
                       help="Directory to search in (default: current directory)")
    parser.add_argument("-c", "--case-sensitive", action="store_true",
                       help="Case-sensitive search")
    parser.add_argument("-r", "--regex", action="store_true",
                       help="Use regular expressions")
    parser.add_argument("-w", "--whole-word", action="store_true",
                       help="Match whole words only")
    parser.add_argument("-f", "--file-pattern", 
                       help="File pattern to match (regex)")
    parser.add_argument("-m", "--max-results", type=int,
                       help="Maximum number of matches to show")
    parser.add_argument("--include", nargs="+", dest="include_dirs",
                       help="Only search in these directories")
    parser.add_argument("--exclude", nargs="+", dest="exclude_dirs",
                       help="Exclude these directories from search")
    
    args = parser.parse_args()
    
    searcher = CodebaseSearcher()
    searcher.search_codebase(
        root_dir=args.directory,
        pattern=args.pattern,
        file_pattern=args.file_pattern,
        case_sensitive=args.case_sensitive,
        regex=args.regex,
        whole_word=args.whole_word,
        max_results=args.max_results,
        include_dirs=args.include_dirs,
        exclude_dirs=args.exclude_dirs
    )

if __name__ == "__main__":
    main()
