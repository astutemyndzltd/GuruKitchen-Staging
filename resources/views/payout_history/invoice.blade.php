<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
      
      .clearfix:after {
        content: "";
        display: table;
        clear: both;
      }
      
      a {
        color: #0087C3;
        text-decoration: none;
      }
      
      body {
        position: relative;
        width: 21cm;  
        height: 29.7cm; 
        margin: 0 auto; 
        color: #555555;
        background: #FFFFFF; 
        font-family: Arial, sans-serif; 
        font-size: 14px; 
        font-family: SourceSansPro;
      }
      
      header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
      }
      
      #logo {
        float: left;
        margin-top: 18px;
      }
      
      #logo img {
        height: 70px;
      }
      
      main {
        margin-top: 35px;
      }
      
      
      #company {
        float: right;
        text-align: right;
      }
      
      
      #details {
        margin-bottom: 50px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      
      #client {
        padding-left: 6px;
        border-left: 6px solid #0087C3;
        float: left;
      }
      
      #client .to {
        color: #777777;
      }
      
      h2.name {
        font-size: 1.4em;
        margin: 0;
        margin-top: 3px;
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
        color: #0087C3;
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
        color: #0087C3;
        font-size: 1.2em;
        font-weight: normal;
        margin: 0 0 0.2em 0;
      }
      
      table .no {
        color: #FFFFFF;
        background: #0087C3;
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
        background: #0087C3;
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
        border-left: 6px solid #0087C3;  
      }
      
      #notices .notice {
        font-size: 1.2em;
      }
      
      footer {
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
    <header class="clearfix">
      <div id="logo">
        <img src="{{ url('/storage/app/public/logos/logo2.png') }}">
      </div>
      <div id="company">
        <h2 class="name">Guru Kitchen Ltd</h2>
        <div class="address">Unit 7 Cooper Way, Parkhouse, Carlisle, Cumbria, United Kingdom, CA3 0JG</div>
        <div>accounts@gurukitchen.co.uk</div>
        <div>VAT: XYZABCPQRZ</div>
      </div>
      </div>
    </header>
    <main>
      <div id="details" >
        <div id="client">
          <div class="to">INVOICE TO:</div>
          <h2 class="name">Quarter Lounge</h2>
          <div class="address">Unit 7 Cooper Way, Parkhouse, Carlisle, Cumbria, United Kingdom, CA3 0JG</div>
        </div>
        <div id="invoice">
          <h1>TAX INVOICE #3256</h1>
          <div class="date"><b>Issue Date:</b> 22 Feb 2021</div>
          <div class="date"><b>Period Covered:</b> 15 Feb 2021 - 22 Feb 2021</div>
        </div>
      </div>

      <h2 class="heading">GuruKitchen Commission</h2>
      <table cellspacing="0" cellpadding="0">


        <thead>
          <tr>
            <th class="no">Orders</th>
            <th class="desc">Total Order Value</th>
            <th class="unit">GuruKitchen Commission</th>
            <th class="qty">VAT</th>
            <th class="total">Gross Commission</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="no">95</th>
            <td class="desc">£3,006.70</th>
            <td class="unit">£420.91 at 14.00%</th>
            <td class="qty">£84.25 at 20.00%</th>
            <td class="total">£505.16</th>
          </tr>
        </tbody>
      </table>

      <h2 class="heading">Invoice Total</h2>
      <table cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">Net</th>
            <th class="desc">VAT Amount</th>
            <th class="unit">Total</th>
            <th class="qty">Restaurant Payout</th>
            <th class="total">Scheduled Payment Date</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="no">£420.91</th>
            <td class="desc">£84.25</th>
            <td class="unit">£505.16</th>
            <td class="qty">£2,501.54</th>
            <td class="total">27 Feb 2021</th>
          </tr>
        </tbody>
      </table>


      <h2 class="heading thanks">No balance due</h2>

      <div id="notices">
        <div class="notice">Invoice for service rendered: food and beverage marketplace platform and delivery.</div>
      </div>

    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>