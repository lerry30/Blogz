const box = ({amb='#1faa'}) => {
  const box = document.createElement('div');
  box.style.cssText = `
    max-width: 96vw;
    min-width: 200px;
    padding: 0.8rem;
    border-radius: 8px;
    border: 1px solid ${amb};
    background-color: var(--com-color);
  `;

  return box;
}

export default box;
