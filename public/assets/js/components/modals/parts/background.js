export const close = () => {
  const bg = document.getElementById('popup-modal');
  bg.remove();
  document.body.style.cssText = `
    height: auto;
    overflow: auto;
  `;
}

const background = () => {
  const bg = document.createElement('div');
  bg.setAttribute('id', 'popup-modal');
  bg.style.cssText = `
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 500;
    background-color: #333c;

    display: flex;
    justify-content: center;
    align-items: center;
  `;

  document.body.append(bg);

  document.body.style.cssText = `
    height: 100vh;
    overflow: hidden;
  `;

  return bg;
}

export default background;
