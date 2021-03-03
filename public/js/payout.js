const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#amount');

const datePickerOptions = { locale: {  format: 'DD MMM YYYY' } };

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

const daterangepicker = $(txtPayoutPeriod).daterangepicker(datePickerOptions, onDateRangeChange);
txtPayoutPeriod.value = '';

$(ddlRestaurants).on('select2:select', function (e) {
    txtAmount.value = '';
    txtPayoutPeriod.value = '';
});

