const slider = () => {
  const logo = document.querySelector('.logo');
  const sidebar = document.querySelector('.slider');
  logo.addEventListener('click', () => {
    if(sidebar.classList.contains('close')) {
      sidebar.style.transform = 'translateX(0)';
      sidebar.classList.remove('close');
    } else {
      sidebar.style.transform = 'translateX(-100%)';
      sidebar.classList.add('close');
    }
//    alert("status: " + sidebar.classList.contains('closed'));
  });
}

export default slider;
