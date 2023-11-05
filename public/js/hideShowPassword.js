const showPassword = $('#show-password');
const passwordField = $('#password');

showPassword.click(function() {
    this.classList.toggle('icon-eye-off');
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
    passwordField.attr('type', type);
});