jQuery(function() {
  const $ = jQuery;

  $('#broken_links_list td .item-content').shorten();
  $('.parent-morelink').click(function(e) {
    e.preventDefault();
    const $item = $(this).parent().find('.parents-content');
    if ($item.hasClass('open')) {
      $item.removeClass('open');
      $(this).text('More');
    } else {
      $item.addClass('open');
      $(this).text('Less');
    }

    return false;
  });

  $('#disable-autofix-form').submit(function(e) {
    $('#disable-autofix-input').val($('#disable-autofix-checkbox').is(':checked') ? 0 : 1);
  });

  $('#activation_form').submit(function(e) {
    showError(false);

    e.preventDefault();
    e.stopPropagation();

    const wrapper = $(this);
    makeLoading(true, wrapper);

    const token = $(this).find('input[name="activationCode"]').val();
    const formData = $(this).serialize();

    sendActivationRequest(token, false, wrapper, function(result) {
      $.post(window.location.href, formData, function() {
        sendActivationRequest(token, true, wrapper, function(result) {
          window.location.reload();
        });
      });
    });

    return false;
  });

  $('#hexometer_links_reload button').click(function() {
    const wrapper = $('#hexometer_links_reload');
    makeLoading(true, wrapper);
    sendActivationRequest($('#property_token').val(), true, wrapper, function(result) {
      window.location.reload();
    });
  });


  function sendActivationRequest(token, loadData, wrapper, callback) {
    const hostname = $('#blog_url').val();
    $.ajax({
      url: 'https://api.hexometer.com/v2/ql',
      type: 'post',
      headers: {
        authorization: token,
      },
      dataType: 'json',
      contentType: "application/json; charset=utf-8",
      data: JSON.stringify({
        query: `
          mutation {
            PropertyOps {
              pluginActivate(address: "${hostname}", platform: "wordpress", reloadData: ${loadData ? 'true' : 'false'}) {
                error,
                message
              }
            }
          }
        `,
      }),
      success: function (result) {
        let errorMessage = false;
        if (result && result.data && result.data.PropertyOps && result.data.PropertyOps.pluginActivate) {
          if (result.data.PropertyOps.pluginActivate.error) {
            errorMessage = result.data.PropertyOps.pluginActivate.message;
          }
        }

        if (errorMessage && errorMessage.length > 0) {
          showError(errorMessage);
          makeLoading(false, wrapper);
          return;
        }

        callback(result);
      },
      error: function() {
        showError('Error connecting to Hexometer.com, please check your internet connection and try again later.');
        makeLoading(false, wrapper);
      }
    });
  }

  function showError(message) {
    const errorWrapper = $('#hexometer_dynamic_errors');
    if (message === false) {
      errorWrapper.html('');
    } else {
      errorWrapper.append(`
        <div class="error notice">
          <p>${message}</p>
        </div>
      `);
    }
  }

  function makeLoading(show = true, wrapper) {
    const $this = wrapper;
    if (show) {
      $this.find('button').addClass('disabled').attr('type', 'button');
      $this.find('.custom-spinner').show();
    } else {
      $this.find('button').removeClass('disabled').attr('type', 'submit');
      $this.find('.custom-spinner').hide();
    }
  }
});
