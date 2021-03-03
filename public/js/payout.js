const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#amount');

async function onDateRangeChange(start, end) {

    let data = {
        restaurantId : $(ddlRestaurants).val(),
        startDate : start.format('YYYY-MM-DD'),
        endDate : end.format('YYYY-MM-DD')
    };  

    let response = await fetch('/restaurantsPayouts/total-order-amount?' + new URLSearchParams(data));
    let json = await response.json();

    txtAmount.value = `£${json.amount}`;
}


$(ddlRestaurants).on('select2:select', async function (e) {
    
    let $dr = $(txtPayoutPeriod).data('daterangepicker');
    $dr && $dr.remove();

    let data = { restaurantId : $(ddlRestaurants).val() };
    let response = await fetch('/restaurantsPayouts/last-payout-date?' + new URLSearchParams(data));
    let json = await response.json();

    let start = moment();
    let end = moment();

    let options = { 
        startDate: start,
        endDate: end,
        locale: {  format: 'DD MMM YYYY' }, 
        minDate: moment(json.date, 'YYYY-MM-DD').add(1, 'days').toDate() 
    };

    $(txtPayoutPeriod).daterangepicker(options, onDateRangeChange);
    
    onDateRangeChange(start, end);

});

$(ddlRestaurants).trigger('select2:select');

