const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#amount');

const datePickerOptions = { locale: {  format: 'DD MMM YYYY' } };

function onDateRangeChange(start, end) {
    let restaurantId = $(ddlRestaurants).val();
    console.log(restaurantId);
    let startDate = start.format('YYYY-MM-DD');
    let endDate = end.format('YYYY-MM-DD');    
}

const daterangepicker = $(txtPayoutPeriod).daterangepicker(datePickerOptions, onDateRangeChange);
txtPayoutPeriod.value = '';

