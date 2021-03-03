const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#payout_amount');
const txtStartDate = document.querySelector('#from_date');
const txtEndDate = document.querySelector('#endDate');
const txtPayoutAmount = document.querySelector('#amount');
const btnSavePayout = document.querySelector('button.btn.btn-primary');


async function onDateRangeChange(start, end) {

    $(btnSavePayout).attr('disabled', true);

    let data = {
        restaurantId : $(ddlRestaurants).val(),
        startDate : start.format('YYYY-MM-DD'),
        endDate : end.format('YYYY-MM-DD')
    };  

    let response = await fetch('/restaurantsPayouts/total-order-amount?' + new URLSearchParams(data));
    let json = await response.json();

    txtStartDate.value = data.startDate;
    txtEndDate.value = data.endDate;
    txtPayoutAmount.value = json.amount;

    txtAmount.value = `£${json.amount}`;

    $(btnSavePayout).attr('disabled', false);

}


$(ddlRestaurants).on('select2:select', async function (e) {

    $(btnSavePayout).attr('disabled', true);
    
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

