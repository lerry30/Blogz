const slide = () => {
  const logo = document.querySelector('.logo');
  const sidebar = document.querySelector('header nav ul');
  logo.addEventListener('click', () => {
    if(sidebar.classList.contains('closed')) {
      sidebar.style.transform = 'translateX(0)';
      sidebar.classList.remove('closed');
    } else {
      sidebar.style.transform = 'translateX(-100%)';
      sidebar.classList.add('closed');
    }
//    alert("status: " + sidebar.classList.contains('closed'));
  });
}

export default slide;
