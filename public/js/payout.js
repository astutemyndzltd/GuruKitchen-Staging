const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#payout_amount');
const txtStartDate = document.querySelector('#from_date');
const txtEndDate = document.querySelector('#to_date');
const txtPayoutAmount = document.querySelector('#amount');
const btnSavePayout = document.querySelector('button.btn.btn-primary');
const txtOrders = document.querySelector('#orders');
const txtGrossRevenue = document.querySelector('#gross_revenue');
const txtAdminCommission = document.querySelector('#admin_commission');
const txtTax = document.querySelector('#tax');


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
    txtOrders.value = json.orders;
    txtGrossRevenue.value = json.gross_revenue;
    txtAdminCommission.value = json.admin_commission;
    txtTax.value = json.tax;

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

    let start = moment(json.startdate, 'YYYY-MM-DD');
    let end = moment(json.enddate, 'YYYY-MM-DD');

    let options = { 
        startDate: start,
        endDate: end,
        locale: {  format: 'DD MMM YYYY' }, 
        minDate: start,
        maxDate: end,
        pickStartDate : false 
    };

    if(options.minDate.isAfter(options.maxDate)) return;

    $(txtPayoutPeriod).daterangepicker(options, onDateRangeChange);

    $(txtPayoutPeriod).on('click.daterangepicker', function() { console.log('welcome to the jungle'); });
    
    onDateRangeChange(start, end);

});

$(ddlRestaurants).trigger('select2:select');

