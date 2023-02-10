function updateCustomerId() {
    $(document).ready(function () {
        var userDisplayField = $('#displayCustomerId');
        var oldUserId = userDisplayField[0].value;
        var selectedUserId = $('#customerId')[0].value;
        userDisplayField.attr('value', selectedUserId);

        $.ajax({
            url: 'includes/ajax_user_process.php?cuid=' + selectedUserId,
            cache: false,
            success: function (response) {
                var obj = JSON.parse(response);
                var customertype = obj['customertype'];
                $('.customer-type').html(customertype.charAt(0).toUpperCase() + customertype.slice(1));
                $('.customer-type').val(customertype);
console.log(response)
                var cardStatus;
                if (obj['loyaltycard'] == "active") {
                    cardStatus = "Active";
                }

                if (obj['loyaltycard'] == "inactive") {
                    cardStatus = "Inactive";
                }

                if (obj['loyaltycard'] == "removed") {
                    cardStatus = "Removed";
                }

                if (!obj['loyaltycard'] || obj['loyaltycard'] == false) {
                    cardStatus = "Doesn\'t have";
                }

                // var loyaltycard = obj['loyaltycard'] == 'true' ? 'Has' : 'Doesn\'t have';
                $('.lcard').html(cardStatus.charAt(0).toUpperCase() + cardStatus.slice(1));
                $('.lcard').val(cardStatus);
            }
        });
    });
}

function articleDropdown(articleType, articleStatus) {
    var selectionType = document.getElementById('articleType');
    for (var i = 0; i < selectionType.options.length; i++) {
        if (selectionType.options[i].value == articleType) {
            selectionType.options[i].setAttribute('selected', true);
        }
    }

    var selectionStatus = document.getElementById('articleStatus');
    for (var k = 0; k < selectionStatus.options.length; k++) {
        if (selectionStatus.options[k].value == articleStatus) {
            selectionStatus.options[k].setAttribute('selected', true);
        }
    }

    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    var doneValue = params.done;
    var textareaElement = document.getElementById('comment');
    var inputElements = document.getElementsByTagName('input');
    if (doneValue === 'true') {
        selectionType.setAttribute('disabled', true);
        selectionStatus.setAttribute('disabled', true);
        selectionType.style.backgroundColor = '#EEEFF0';
        selectionStatus.style.backgroundColor = '#EEEFF0';
        textareaElement.setAttribute('readonly', true);
        textareaElement.style.backgroundColor = '#EEEFF0';
        for (var j = 0; j < inputElements.length; j++) {
            inputElements[j].setAttribute('readonly', true);
            inputElements[j].style.backgroundColor = '#EEEFF0';
        }
    }
}

function customerDropdown(customerType, lcStatus) {
    var selectionType = document.getElementById('customerType');
    for (var i = 0; i < selectionType.options.length; i++) {
        if (selectionType.options[i].value == customerType) {
            selectionType.options[i].setAttribute('selected', true);
        }
    }

    var selectionLCard = document.getElementById('customerLCard');
    for (var k = 0; k < selectionLCard.options.length; k++) {
        if (selectionLCard.options[k].value == lcStatus) {
            selectionLCard.options[k].setAttribute('selected', true);
        }
    }

    const params = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });
    var doneValue = params.done;
    var textareaElement = document.getElementById('comment');
    var inputElements = document.getElementsByTagName('input');
    if (doneValue === 'true') {
        selectionType.setAttribute('disabled', true);
        selectionLCard.setAttribute('disabled', true);
        selectionType.style.backgroundColor = '#EEEFF0';
        selectionLCard.style.backgroundColor = '#EEEFF0';
        textareaElement.setAttribute('readonly', true);
        textareaElement.style.backgroundColor = '#EEEFF0';
        for (var j = 0; j < inputElements.length; j++) {
            inputElements[j].setAttribute('readonly', true);
            inputElements[j].style.backgroundColor = '#EEEFF0';
        }
    }
}

function removeArticleFromOrder(removeItemId, orderItemsRowId, itemQuantity, itemPrice, oldPrice) {
    var newPrice = oldPrice - (itemQuantity * itemPrice);

    $.ajax({
        data: {'removeRowId': orderItemsRowId},
        url: 'includes/ajax_remove_item.php',
        context: this,
        type: 'post',
        success: function (resposne) {
            $('#article-block' + removeItemId).empty();
            $('#itemId' + removeItemId).empty();
            $('#total-price').html(newPrice + " <i style='color: limegreen'>(updated price)</i>");
        }
    });
}

function urlAdjustment(sort, perPage, fileName) {
    history.pushState(null, null, fileName + "?page=1&sort=" + sort + "&perpage=" + perPage);
}