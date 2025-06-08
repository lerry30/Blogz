import background from './parts/background.js';
import box from './parts/box.js';

const close = () => {
  const bg = document.getElementById('popup-modal');
  bg.remove();
}

const warningModal = (
    title='',
    message='',
    onSubmit = () => {},
    onCancel = () => {}
  ) => {
  const bg = background();
  const bx = box({amb:'#e11a'});
  const mTitle = document.createElement('h3');
  const mssg = document.createElement('p');
  const btnSubmit = document.createElement('button');
  const btnCancel = document.createElement('button');
  const btnCont = document.createElement('div');
  mTitle.textContent = title;
  mssg.textContent = message;
  btnSubmit.classList.add('btn');
  btnCancel.classList.add('btn');
  btnSubmit.textContent = 'Proceed';
  btnCancel.textContent = 'Cancel';

  btnSubmit.style.backgroundColor = '#e007';
  btnCancel.style.backgroundColor = '#232323';

  btnCont.style.cssText = `
    display: flex;
    justify-content: flex-end;
    gap: 0.4rem;
  `;

  btnSubmit.addEventListener('click', () => {
    if(onSubmit)
      onSubmit();
    close();
  });

  btnCancel.addEventListener('click', () => {
    if(onCancel)
      onCancel();
    close();
  });

  btnCont.append(btnSubmit);
  btnCont.append(btnCancel);

  bx.append(mTitle);
  bx.append(mssg);
  bx.append(btnCont);
  bg.append(bx);
  document.body.append(bg);
}

export default warningModal;
