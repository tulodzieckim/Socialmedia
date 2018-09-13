$(document).ready(function() {

   $("#toSignup").on('click', function() {
      $('#login-section').slideUp('slow', function() {
         $('#signup-section').slideDown('slow');
      })
   });

   $('#toLogin').on('click', function() {
      $('#signup-section').slideUp('slow', function() {
         $('#login-section').slideDown('slow');
      })
   });

})