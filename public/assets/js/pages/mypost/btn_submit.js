import warningModal from '../../components/modals/warning_modal.js';

export const deleteBlogPost = () => {
  const delForms = Array.from(document.querySelectorAll('.del-post'));
  delForms.forEach(form => {
    const btn = form.querySelector('button');
    btn.addEventListener('click', () => {
      warningModal(
        'Post Remove',
        'Are you sure do you want to remove this post',
        () => {
          form.submit();
        },
        () => {}
      );
    });
  });
}
