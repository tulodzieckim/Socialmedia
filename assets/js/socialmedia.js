$(document).ready(function () {

   let resizeTimeout;
   $(window).resize(function () {
      if (resizeTimeout) {
         clearTimeout(resizeTimeout)
      }
      resizeTimeout = setTimeout(function () {
         if (window.innerWidth < 768) {
            $('#profile-sidebar').addClass('sidebar-hidden');
            $('#collapse-btn').addClass('hidden');
            $('#profile-wrapper').addClass('hidden');
         }
      }, 100)
   })



   $('#collapse-btn').on('click', function () {
      $('#profile-sidebar').toggleClass('sidebar-hidden');
      $('#collapse-btn').toggleClass('hidden');
      $('#profile-wrapper').toggleClass('hidden');
   });


   $('#submitProfilePost').on("click", function () {
      $.ajax({
         url: "includes/handlers/ajax_submit_profile_post.php",
         type: "post",
         data: $('form.profile-post').serialize(),
      }).done(function (response) {
         $('#post-form').modal('hide');
         location.reload();
      }).fail(function (response) {
         alert('error: ' + response);
      });
   });

   // submit the form after click on DIV with font awesome icon
   $('#search-icon').on('click', function() {
      document.getElementsByName('search_form')[0].submit();
   })

   // close dropdowns
   $(document).on('click', function() {
      $('.dropdown-menu').removeClass('show');
   })

});

function onClickDeleteBtn(event, id) {
   const confirmModal = $('#delete-modal' + id);
   confirmModal.modal('show');
   // $('#yes-btn').on('click', eventHandler(id));

}

function onYesBtn(event) {
   $.post("includes/form_handlers/delete_post.php?post_id=" + event.target.dataset.id);
   location.reload();
}

function onNoBtn(event) {
   const confirmModal = $('#delete-modal' + event.target.dataset.id);
   confirmModal.modal('hide');
}

function getUsers(value, user) {
   $.ajax({
      url: "includes/handlers/ajax_friend_search.php",
      type: 'POST',
      data: {
         query: value,
         userLoggedIn: user
      }
   }).done(function (response) {
      $("#results").html(response);
   })

}

function getDropdownData(user, type, selector) {
   const messagesDropdownMenu = $(selector);

   if (!messagesDropdownMenu.hasClass('show')) {
      let pageName;

      switch (type) {
         case 'notification':
            pageName = 'ajax_load_notifications.php';
            $("#unread-notifications").remove();         
            break;
         case 'message':
            pageName = 'ajax_load_messages.php';
            $("#unread-messages").remove();
            break;
      }

      const ajaxreq = $.ajax({
         url: "includes/handlers/" + pageName,
         type: "POST",
         data: {
            page: 1,
            userLoggedIn: user,
         },
         cache: false
      }).done(function (response) {
         messagesDropdownMenu.html(response);
         messagesDropdownMenu.toggleClass('show');
         $("#dropdown-data-type").val(type);
      });

   } else {
      messagesDropdownMenu.html("");
      messagesDropdownMenu.removeClass('show');

   }

}


function getLiveSearchUsers(searchedValue, user) {
   $('#search-results-dropdown').addClass('show');
   $('#search-results-dropdown .dropdown-menu').addClass('show');

   $.ajax({
      url: "includes/handlers/ajax_search.php",
      type: "POST",
      data: {
         searchedValue: searchedValue,
         userLoggedIn: user
      }
   }).done(function (response) {

      $('#search-results-dropdown').addClass('show');
      const searchFooter = $('#search-results-footer');

      if(searchFooter.hasClass('empty')) {
         $("#search-results-footer").removeClass('empty');
      }

      $('#search-results').html(response);
      searchFooter.html(`<a href='search.php?q=${searchedValue}'>See All Results</a>`);
      $('#search-results').append(searchFooter);

      if(!Boolean(response)) {
         searchFooter.html("");
         searchFooter.addClass('empty');
         $('#search-results').removeClass('show');
      }

   });
}