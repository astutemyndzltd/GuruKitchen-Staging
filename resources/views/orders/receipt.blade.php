<div id="receipt">
    <img src="/storage/app/public/logos/logo.png" alt="brand-logo" id="logo">
	
    <h2 id="order-id">#{{ $orderDetails['id'] }}</h2>
	
	@if($orderDetails['hint'] != null)
	<div id="hint" class="intro-row">
    	<i class="fal fa-comment-alt-lines"></i>
        <span>{{ $orderDetails['hint'] }}</span>
    </div>
	@endif

	@if($orderDetails['customer_name'] != null)
	<div id="customer-name" class="intro-row">
   		<i class="fal fa-user"></i>
   		<span>{{ $orderDetails['customer_name'] }}</span>
    </div>
	@endif
	
	@if($orderDetails['customer_phone'] != null)
	<div id="customer-phone" class="intro-row">
   		<i class="fal fa-phone"></i>
   		<span>{{ $orderDetails['customer_phone'] }}</span>
    </div>
	@endif
	
	@if($orderDetails['delivery_address'] != null)
	<div id="delivery-address" class="intro-row">
        <i class="fal fa-map-marker-alt"></i>
        <span>{{ $orderDetails['delivery_address'] }}</span>
    </div>
    @endif
    
    @if($orderDetails['order_type'] != null)
	<div id="delivery-address" class="intro-row">
        <i class="fal fa-truck-couch"></i>
        <span>{{ $orderDetails['order_type'] }}</span>
    </div>
	@endif

    @if($orderDetails['preorder_info'] != null)
	<div id="preorder-info" class="intro-row">
        <i class="fal fa-tag"></i>
        <span>Pre-Order | Expected by {{ $orderDetails['preorder_info'] }}</span>
    </div>
	@endif
	
	@if($orderDetails['payment_method'] != null)
	<div id="delivery-address" class="intro-row">
        <i class="fal fa-credit-card"></i>
        <span>{{ $orderDetails['payment_method'] }}</span>
    </div>
	@endif
 	
	@if($orderDetails['restaurant_name'] != null)
	<div id="restaurant-name" class="intro-row">
  		<i class="fal fa-utensils"></i>
   		<span>{{ $orderDetails['restaurant_name'] }}</span>
    </div>
	@endif

	@if($orderDetails['payment_method'] != 'Pay on Pickup' and $orderDetails['driver_name'] != null)
	<div id="driver-name" class="intro-row">
        <i class="fal fa-running"></i>
        <span>{{ $orderDetails['driver_name'] }}</span>
    </div>
	@endif

    @if($orderDetails['order_note'] != '')
	<div id="order-note" class="intro-row">
        <i class="fal fa-clipboard"></i>
   		<span>{{ $orderDetails['order_note'] }}</span>
    </div>
	@endif

    
    <div class="marker"></div>

    <div id="foods">
		
		@foreach($orderDetails['food_categories'] as $fc)
		<div class="food-category">
            <h4 class="category-name">{{ $fc['name'] }}</h4>
			
				@foreach($fc['foods'] as $food)
				<div class="food">
                    <div class="food-row">
                        <span class="food-quantity">{{ $food['quantity'] }}x</span>
                        <span class="food-name">{{ $food['name'] }}</span>
                        <span class="food-price">{{ getPriceOnly($food['price']) }}</span>
                    </div>
                    
					@foreach($food['extras'] as $extra)
					<div class="extra-row">
                        <span class="extra-quantity"></span>
                        <span class="extra-name">{{ $extra['name'] }}</span>
						@php $extraPrice = getPriceOnly($extra['price']); @endphp
                        <span class="extra-price">{{ $extraPrice == '£0.00' ? '--' : $extraPrice }}</span>
                    </div>
					@endforeach
					
                </div>
				@endforeach              
        </div>
		@endforeach		        
    </div>

    <div class="marker"></div>

    <div id="subtotal" class="outro-row">
        <span>Subtotal</span>
        <span>{{ getPriceOnly($orderDetails['subtotal']) }}</span>
    </div>

    <div id="delivery-fee" class="outro-row">
        <span>Delivery Fee</span>
        <span>{{ getPriceOnly($orderDetails['delivery_fee']) }}</span>
    </div>

    {{-- <div id="tax" class="outro-row">
        <span>Tax ({{ $orderDetails['tax'] }}%)</span>
        <span>{{ getPriceOnly($orderDetails['tax_amount']) }}</span>
    </div> --}}

    <div class="marker"></div>

    <div id="total" class="outro-row">
        <h4>Total</h4>
        <h4>{{ getPriceOnly($orderDetails['total']) }}</h4>
    </div>
</div>