<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Invoice Example</title>
    </head>
    <body>
    <?php
    for ($i = 0; $i < 10; $i++) { ?>
    <table style="width: 545pt;margin:200px auto 30px">
        <thead>
            <tr>
                <td style="width:33.33%;padding-bottom: 10pt;vertical-align: middle;"><b>Debit Memo</b></td>
                <td style="width:33.33%;padding-bottom: 10pt;vertical-align: middle" align="center"><h2 class="font-big" style="font-weight: bold;">Tax Invoice</h2></td>
                <td style="width:33.33%;padding-bottom: 10pt;vertical-align: middle" align="right"><b>Triplicate For Supplier</b></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3">
                    <div style="border: 1px solid #000;width: 100%;">
                        <table cellspacing="0" style="width: 100%;" class="table-top">
                            <tr>
                                <td style="width: 33.33%;">
                                    <p style="text-decoration: underline;">Details of Receiver (Billed To)</p>
                                    <h3 style="margin-bottom:0">Dimitie Kendall</h3>
                                    <h3 style="margin-bottom:2px; margin-top: 2px;">(Upwork Group)</h3>
                                    <p style="margin-bottom: 5px;"><b>Attn: Dimitie Kendall</b></p>
                                    <p>PO Box 1368 <br/>Paradise Point, <br/>4216 Australia</p>
                                </td>
                                <td style="width: 33.33%;border-left: 1px solid #000;height: 134px;">
                                    <p style="text-decoration: underline;">Details of payer as a mediator</p>
                                    <h3>Upwork Global Inc</h3>
                                    <h3 style="margin-bottom:2px; margin-top: 2px;"></h3>
                                    <p style="margin-bottom: 5px;"><b>Global HQ,</b></p>
                                    <p>475 Brannan St., <br/>Suite 430, San Francisco, <br/>CA, 94107 <br/>United States</p>
                                </td>
                                <td style="width: 33.33%;border-left: 1px solid #000;padding: 0;" valign="middle">
                                   <div style="height: 66px;padding-top:13px">
                                    <table cellspacing="0" style="width: 100%;padding: 0 10px;">
                                        <tr>
                                            <td style="width: 40%;padding: 5px;"><b>Invoice No.</b></td> 
                                            <td style="padding: 5px;"><b>:TI/47</b></td> 
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;padding: 5px;"><b>Invoice Date</b></td> 
                                            <td style="padding: 5px;"><b>:</b></td> 
                                        </tr>
                                    </table>
                                    </div>
                                    <div style="border-top: 1px solid #000;padding-top:13px">
                                    <table cellspacing="0" style="width: 100%;padding: 0 10px;">
                                        <tr>
                                            <td style="width: 40%;padding: 5px;"><b>Broker</b></td> 
                                            <td style="padding: 5px;">:Upwork Global Inc.</td> 
                                        </tr>
                                        <tr>
                                            <td style="width: 40%;padding: 5px;"><b>Reference ID</b></td> 
                                            <td style="padding: 5px;">:</td> 
                                        </tr>
                                    </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table cellspacing="0" class="priceTable" style="width: 100%;border-bottom: 1px solid #000;">
                            <tr>
                                <th style="width: 4%;background: #eee;">Sr. No</th>
                                <th style="width: 29.3%;background: #eee;">Description of Services</th>
                                <th style="width: 16.65%;background: #eee;">HSN/SAC</th>
                                <th style="width: 16.65%;background: #eee;">USD/GBP/EUR</th>
                                <th style="width: 16.65%;background: #eee;">Conversion Rate <br/>In INR</th>
                                <th style="width: 16.65%;background: #eee;border-right: 0;">Taxable Amount (Amount in INR)</th>
                            </tr>
                            <tr>
                                <td style="width: 4%;">1</td>
                                <td style="width: 29.3%;text-align: left;">
                                    <b>Web Design & Development Service</b>
                                    
                                </td>
                                <td style="width: 16.65%;">998314</td>
                                <td style="width: 16.65%;">USD</td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;border-right: 0;">
                                   </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td style="width: 29.3%;text-align: left;height: 210px;"></td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;border-right: 0;"></td>
                            </tr>
                        </table>
                        <table cellspacing="0" class="priceTable" style="width: 100%;border-bottom: 1px solid #000;">
                            <tr>
                                <td style="width: 33.3%;">&nbsp;</td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;"></td>
                                <td style="width: 16.65%;border-right: 0;"></td>
                            </tr>
                        </table>
                        <table cellspacing="0" class="pricetotalMain" style="width: 100%;border-bottom: 1px solid #000;">
                            <tr>
                                <td style="width: 66.4%;border-right: 1px solid #000;">
                                    <table cellspacing="0" style="width: 100%;">
                                        <tr>
                                            <td colspan="2" style="background: #eee;width: 50%;border-bottom: 1px solid #000;text-align: center;padding: 3px;">
                                                <b>Supplier Details</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%;padding: 5px 5px 0;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 8px;">GSTIN</td> 
                                                        <td><b>:24AQYPG3098E1ZI</b></td> 
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 15px;">State</td> 
                                                        <td><b>:24 - Gujarat</b></td> 
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="width: 50%;padding: 5px 5px 0;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 40%;">PAN No</td> 
                                                        <td><b>:AQYPG3098E</b></td> 
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="width: 50%;border-bottom: 1px solid #000;border-top: 1px solid #000;background: #eee;text-align: center;padding: 3px;"><b>Bank Detail</b></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%;padding: 5px 5px 0;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 5px;">Bank Name</td> 
                                                        <td><b>:HDFC BANK LIMITED</b></td> 
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 5px;">Branch</td> 
                                                        <td><b>:SIHOR</b></td> 
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="width: 50%;padding: 0 15px 10px;"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50%;padding: 0 5px 10px;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 5px;">Account Number</td> 
                                                        <td><b>:50200058443602</b></td> 
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 5px;">Account Type</td> 
                                                        <td><b>:CURRENT</b></td> 
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="width: 50%;padding: 0 15px 10px;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 5px;">IFSC Code</td> 
                                                        <td><b>:HDFC0002144</b></td> 
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 40%;padding-bottom: 5px;">Swift Code</td> 
                                                        <td><b>:HDFCINBB</b></td> 
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 33.2%;">
                                    <table class="amoutPrice" cellspacing="0" style="width: 100%;">
                                        <tr>
                                            <td style="width: 50.1%;border-right: 1px solid #000;padding: 15px 5px 5px">Taxable Amount...</td>
                                            <td style="width: 49.9%;text-align: right;padding: 15px 5px 0"></td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50.1%;border-right: 1px solid #000;padding: 0 5px 5px">CGST 0%...</td>
                                            <td style="width: 49.9%;text-align: right;padding: 0 5px">0</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50.1%;border-right: 1px solid #000;padding: 0 5px 5px">SGST 0%...</td>
                                            <td style="width: 49.9%;text-align: right;padding: 0 5px;">0</td>
                                        </tr>
                                        <tr>
                                            <td style="width: 50.1%;border-right: 1px solid #000;padding: 80px 5px 10px">Round Off... </td>
                                            <td style="width: 49.9%;text-align: right;padding: 90px 5px 10px;">
                                               
                                            </td>
                                        </tr>
                                        <tr style="background: #eee;">
                                            <td style="width: 49%;border-right: 1px solid #000;padding: 8px 5px;border-top: 1px solid #000;background: #eee;"><b>Bill Amount </b></td>
                                            <td style="width: 50.1%;text-align: right;padding: 8px 5px;border-top: 1px solid #000;background: #eee;"><b>
                                                
                                            </b></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <table cellspacing="0" style="width: 100%;padding: 0;border-bottom: 1px solid #000;">
                            <tr>
                                <td style="width: 100%;padding: 5px 10px;"><b> Bill Amount In Words :   Rupees Only</b></td>
                            </tr>
                        </table>
                        <table cellspacing="0" style="width: 100%;padding: 10px;">
                            <tr>
                                <td style="width: 80%;">
                                    <h3 style="margin-top: 0;">Notes:</h3>
                                    <table>
                                        <tr>
                                            <td style="width: 10px;">-</td>
                                            <td style="padding-bottom: 10px;">Foreign Exchange Rates are as per Reserve Bank of India (RBI) <br/>Exchange Rates on https://rbi.org.in/.</td>
                                        </tr>
                                        <tr>
                                            <td>-</td>
                                            <td>This is a computer generated invoice.</td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 20%;text-align: center;">
                                    <h3 style="margin-top: 0;margin-bottom: 45pt;">For tviStech</h3>
                                    <h4>Authorised Signatory</h4>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="page-break"></div>
<?php } ?>
    

<style>
    .page-break {
         /*page-break-after: always;*/
    }
    .page-break:last-child {
        /*page-break-after: auto;*/
    }
    /*@media print {
    html, body {
        height: 99%;    
    }*/
}
    * {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        font-size: 8.5pt;
        font-weight: normal;
        letter-spacing: normal;
        line-height: 14px;
    }
    table{
        font-family: 'Arial', sans-serif;
        font-weight: normal;
    }
    b{
        font-weight: bold;
    }
    h1 {
        color: #c8102e;
        font-size: 20pt;
        font-style: normal;
        font-weight: bold;
        line-height: 22px;
        margin-bottom: 13px;
        padding: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    h3 {
        color:#000000;
        font-size:10.5pt;
        font-style:normal;
        font-weight:bold;
        line-height:15px;
        margin-bottom:15px;
        margin-top:15px;
        letter-spacing: 1px;
    }
    h4 {
        color:#000000;
        font-size:8.5pt;
        font-style:normal;
        font-weight:bold;
        line-height:13px;
        margin-bottom:0;
        margin-top:0;
        letter-spacing: 1px;
    }

    td {
        vertical-align: top;
    }
    .font-big{
        font-size:13pt;
        font-style:normal;
        font-variant:normal;
        font-weight:normal;
        line-height:13pt;
        margin-bottom:0;
    }
    
    .p-body-sans-3px a {
        color:#000000;
        text-decoration:underline;
    }
    
    .pCenter{text-align: center;}
    .table-top{
        border-bottom: 1px solid #000;
        padding: 0;
    }
    .table-top td{
        padding: 10px;
    }
    .mb-3pt{margin-bottom: 3pt;}
    .priceTable th,.priceTable td{
        border-right: 1px solid #000;
        text-align: center;
        padding: 5px 5px;
    }
    .priceTable th{
        vertical-align: middle;
        font-weight: bold;
        border-bottom: 1px solid #000;
    }
    .amoutPrice td{
        padding: 0 10px;
    }
</style>       
</body>
</html>


