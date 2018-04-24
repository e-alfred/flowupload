$('li#app-navigation-entry-utils-create').on('click', function() {
  $('li#app-navigation-entry-utils-add').addClass('editing');
});

$('li#app-navigation-entry-utils-add .icon-close').on('click', function () {
  $('li#app-navigation-entry-utils-add').removeClass('editing');
})

$('li#app-navigation-entry-utils-add .icon-checkmark').on('click', function () {
  // Ajouter la destination dans la bdd
  $('li#app-navigation-entry-utils-add').removeClass('editing');
})
