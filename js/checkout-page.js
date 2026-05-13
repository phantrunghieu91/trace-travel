document.addEventListener('DOMContentLoaded', docEvent => {
  // Select all billing input fields
  const billingFields = document.querySelectorAll('.billing-field__wrapper input');
  billingFields.forEach(field => {
    field.addEventListener('change', fieldEvt => {
      field.setAttribute('value', fieldEvt.currentTarget.value);
    });
  });
});
