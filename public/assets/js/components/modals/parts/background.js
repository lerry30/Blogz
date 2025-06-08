const background = () => {
  const bg = document.createElement('div');
  bg.setAttribute('id', 'popup-modal');
  bg.style.cssText = `
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #333c;

    display: flex;
    justify-content: center;
    align-items: center;
  `;

  return bg;
}

export default background;
