<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}" media="all" />
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="/storage/app/public/logos/logo2.png">
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