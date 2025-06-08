const hideSplash = () => {
  const splash = document.querySelector('.splash');
  if(splash) {
    setTimeout(() => {
      splash.remove();
    }, 4000);
  }
}

export default hideSplash;
