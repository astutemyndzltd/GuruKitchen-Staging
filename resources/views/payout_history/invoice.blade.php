<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Invoice</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro&display=swap" rel="stylesheet">
  <style>
    
    @page { size: A4 }

    .clearfix:after {
      content: "";
      display: table;
      clear: both;
    }

    a {
      color: #2f294f;
      text-decoration: none;
    }

    body {
      position: relative;
      width: 21cm;  
      height: 29.7cm; 
      margin: 0 auto; 
      color: #555555;
      background: #FFFFFF; 
      font-size: 14px; 
      font-family: 'Source Sans Pro', sans-serif;
    }

    div#header {
      padding: 10px 0;
      margin-bottom: 20px;
      border-bottom: 1px solid #AAAAAA;
    }

    #logo {
      float: left;
      margin-top: 18px;
    }

    #logo img {
      height: 75px;
    }

    #main {
      margin-top: 35px;
    }


    #company {
      float: right;
      text-align: right;
    }


    #details {
      margin-bottom: 50px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    #client {
      padding-left: 6px;
      border-left: 6px solid #2f294f;
      float: left;
    }

    #client .to {
      color: #777777;
    }

    h2.name {
      font-size: 1.4em;
      margin: 0;
      margin-top: 3px;
      font-weight: 700;
    }

    div.address {
      width: 300px;
      margin-top: 3px;
    }

    div.phone {
      width: 300px;
      margin-top: 3px;
    }

    #company > div {
      margin-top: 3px;
    }

    #invoice {
      float: right;
      text-align: right;
    }

    #invoice h1 {
      color: #26dabf;
      font-size: 2.4em;
      line-height: 1em;
      margin: 0  0 10px 0;
    }

    #invoice .date {
      font-size: 1.1em;
      color: #777777;
      margin-bottom: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 0;
      margin-bottom: 20px;
    }

    table th,
    table td {
      padding: 20px;
      background: #EEEEEE;
      text-align: left;
      border-bottom: 1px solid #FFFFFF;
    }



    table td h3{
      color: #2f294f;
      font-size: 1.2em;
      font-weight: normal;
      margin: 0 0 0.2em 0;
    }

    table .no {
      color: #FFFFFF;
      background: #2f294f;
    }

    table .desc {
      text-align: left;
      width: 3cm;
    }

    table .unit {
      background: #DDDDDD;
    }

    table .total {
      text-align: right  !important;
    }

    table .total {
      background: #2f294f;
      color: #FFFFFF;
    }

    table td.unit,
    table td.qty {
      text-align: left;
    }

    table tbody tr:last-child td {
      border: none;
    }

    table tfoot td {
      padding: 10px 20px;
      background: #FFFFFF;
      border-bottom: none;
      font-size: 1.2em;
      border-top: 1px solid #AAAAAA; 
    }

    table tfoot tr:first-child td {
      border-top: none; 
    }

    table tfoot tr:last-child td {
      color: #57B223;
      font-size: 1.4em;
      border-top: 1px solid #57B223; 

    }

    table tfoot tr td:first-child {
      border: none;
    }

    .thanks{
      margin-bottom: 50px;
      margin-top: 50px;
      color: #9e9e9e;
    }

    #notices{
      padding-left: 6px;
      border-left: 6px solid #2f294f;  
    }

    #notices .notice {
      font-size: 1.2em;
    }

    #footer {
      color: #777777;
      width: 100%;
      height: 30px;
      position: absolute;
      bottom: 0;
      border-top: 1px solid #AAAAAA;
      padding: 8px 0;
      text-align: center;
    }
  </style>
  
</head>

<body>

  <div class="clearfix" id="header">

    <div id="logo">
      <img src="{{ url('images/logo3.png') }}">
    </div>

    <div id="company">
      <h2 class="name">Guru Kitchen Ltd</h2>
      <div class="address">Unit 7 Cooper Way, Parkhouse, Carlisle, Cumbria, United Kingdom, CA3 0JG</div>
      <div>accounts@gurukitchen.co.uk</div>
      <div>VAT: 373 5643 78</div>
    </div>

  </div>

  <div id="main">

    <div id="details">
      <div id="client">
        <div class="to">INVOICE TO:</div>
        <h2 class="name">{{ $payout->restaurant->name }}</h2>
        <div class="address">{{ $payout->restaurant->address }}</div>
      </div>
      <div id="invoice">
        <h1>TAX INVOICE #{{ sprintf('%05d', $payout->id) }}</h1>
        <div class="date"><b>Issue Date: </b>{{ date('d M Y', strtotime($payout->created_at)) }}</div>
        <div class="date"><b>Period Covered: </b>{{ date('d M Y', strtotime($payout->from_date)) }} - {{ date('d M Y', strtotime($payout->to_date)) }}</div>
      </div>
    </div>

    <h2 class="heading">GuruKitchen Commission + Delivery Charges</h2>

    <table cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th class="no">Orders</th>
          <th class="desc">Total Order Value</th>
          <th class="unit">Commission and VAT</th>
          <th class="qty">Delivery Charges</th>
          <th class="total">Total</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="no">{{ $payout->orders }}</td>
          <td class="desc">£{{ number_format($payout->gross_revenue, 2) }}</td>
          <?php
            $adminCommission = round($payout->gross_revenue * $payout->admin_commission / 100, 2);
            $tax = round($adminCommission * $payout->tax / 100, 2);
            $driverCommission = round($payout->driver_commission, 2); 
            $total= $adminCommission + $tax + $driverCommission + $payout->delivery_fee;
          ?>
          <td class="unit">
            GK Commission - £{{ number_format($adminCommission, 2) }} at {{ $payout->admin_commission }}%<br>
            Driver Commission - £{{ number_format($driverCommission, 2) }} at {{ $payout->driver_commission_rate }}%<br>
            VAT - £{{ number_format($tax, 2) }} ({{ $payout->tax }}% of £{{ $adminCommission }})
          </td>
          <td class="qty">£{{ number_format($payout->delivery_fee, 2) }}</td>
          <td class="total">£{{ number_format($total,2) }}</td>
        </tr>
      </tbody>
    </table>

    <h2 class="heading">Invoice Total</h2>
    <table cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th class="no">Gross Revenue</th>
          <th class="desc">Commission + VAT + Delivery Charges</th>
          <th class="unit">Restaurant Payout</th>
          <th class="total">Scheduled Payment Date</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="no">£{{ number_format($payout->gross_revenue, 2) }}</td>
          <td class="desc">£{{ number_format($total, 2) }}</td>
          <td class="unit">£{{ number_format($payout->amount, 2) }}</td>
          <td class="total">{{ date('d M Y', strtotime($payout->created_at. ' + 7 days')) }}</td>
        </tr>
      </tbody>
    </table>


    <h2 class="heading thanks">No balance due</h2>

    <div id="notices">
      <div class="notice">Invoice for service rendered: food and beverage marketplace platform and delivery.</div>
    </div>

  </div>

  <div id="footer">
    Invoice was created on a computer and is valid without the signature and seal.
  </div>

</body>

</html>