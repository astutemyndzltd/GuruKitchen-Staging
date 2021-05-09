const ddlDriver = document.querySelector('#user_id');
const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const txtPayoutAmount = document.querySelector('#payout_amount');
const btnSavePayout = document.querySelector('button.btn.btn-primary');
const txtFromDate = document.querySelector('#from_date');
const txtToDate = document.querySelector('#to_date');
const txtTotalOrders = document.querySelector('#orders');
const txtOrderSubtotal = document.querySelector('#subtotal');
const txtDeliveryFee = document.querySelector('#delivery_fee');
const txtAmount = document.querySelector('#amount');
const txtDriverId = document.querySelector('#driver_id');


async function onDateRangeChange(start, end) {

    $(btnSavePayout).attr('disabled', true);

    let data = {
        driverId : $(ddlDriver).val(),
        startDate : start.format('YYYY-MM-DD'),
        endDate : end.format('YYYY-MM-DD')
    };  

    let response = await fetch('/driversPayouts/payout-amount?' + new URLSearchParams(data));
    let json = await response.json();

    txtDriverId.value = data.driverId;
    txtFromDate.value = data.startDate;
    txtToDate.value = data.endDate;
    txtTotalOrders.value = json.orders;
    txtOrderSubtotal.value = json.orders_subtotal;
    txtDeliveryFee.value = json.delivery_fee;
    txtAmount.value = json.payout_amount; 
    
    txtPayoutAmount.value = `£${json.payout_amount}`;

    $(btnSavePayout).attr('disabled', false);

}



$(ddlDriver).on('select2:select', async function (e) {

    $(btnSavePayout).attr('disabled', true);

    let $dr = $(txtPayoutPeriod).data('daterangepicker');
    $dr && $dr.remove();

    let data = { driverId : $(ddlDriver).val() };

    let response = await fetch('/driversPayouts/payment-period?' + new URLSearchParams(data));
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

    if (options.minDate.isAfter(options.maxDate)) return;

    $(txtPayoutPeriod).daterangepicker(options, onDateRangeChange);

    onDateRangeChange(start, end);

});


$(ddlDriver).trigger('select2:select');