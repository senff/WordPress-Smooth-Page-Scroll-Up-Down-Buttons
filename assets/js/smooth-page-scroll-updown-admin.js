(function($) {
  $(document).ready(function($) {

    checkon = $('#psb_topbutton').is(':checked');
    setOptions(checkon);
    highlightOptions();

    $('#psb_topbutton').on('change',function(){
      checkon = $('#psb_topbutton').is(':checked');
      setOptions(checkon);
    });

    $('.positioning-buttons input:radio').on('change',function(){
      highlightOptions();
    });

  });


  function setOptions(withTopButton) {
    if(withTopButton) {
      $('.positioning-buttons').addClass('with-top-button');
    } else {
      $('.positioning-buttons').removeClass('with-top-button');
    }
  }

  function highlightOptions() {
    $('.positioning-option').removeClass('selected');
    $('.positioning-buttons input:radio').each(function(i) {
      if ($(this).is(':checked')) { 
        $(this).parent().addClass('selected');  
      } 
    });
  }

}(jQuery));


