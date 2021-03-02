const txtPayoutPeriod = document.querySelector('#txtPayoutPeriod');
const ddlRestaurants = document.querySelector('#restaurant_id');
const txtAmount = document.querySelector('#amount');

let datePickerOptions = {
    locale: { 
        format: 'DD MMM YYYY'
    }
};


function onDateRangeChange(start, end) {

}


const daterangepicker = $(txtPayoutPeriod).daterangepicker(datePickerOptions, onDateRangeChange);