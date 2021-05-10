var express = require('express')
var bodyParser = require('body-parser')

var dotenv = require('dotenv')
var jwt = require('jsonwebtoken')

dotenv.config()
 
var app = express()
 
const port = process.env.PORT || 3001


const { Pool } = require('pg');
const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: {
    rejectUnauthorized: false
  }
});


app.use(bodyParser.urlencoded({extended:false}));
app.use(bodyParser.json());
app.use(bodyParser.raw());

app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
});

app.post("/send", async function(req,res){
  
  var PaymentSum = req.body.PaymentSum;
  var DebitedAccountNumber = req.body.DebitedAccountNumber;
  var DebitedNameAndAddress = req.body.DebitedNameAndAddress;
  var CreditedAccountNumber = req.body.CreditedAccountNumber;
  var CreditedNameAndAddress = req.body.CreditedNameAndAddress;
  var Title = req.body.Title;
  var Amount = req.body.Amount;

  var v1=CreditedAccountNumber.substr(3,4);
  var v2=CreditedAccountNumber.substr(8,4);

  BankNo=v1+v2;

  var data = new Date();
  var dataa=data.getDate()+"-"+(data.getMonth()+1)+"-"+data.getFullYear()+" "+(data.getHours()+1)+":"+data.getMinutes()
  
    if(Amount<=0){
      res.send("Ujemna");
    }

    try{
    const client = await pool.connect();
    const result = await client.query("INSERT into przelewy(bankno,paymentsum,debitedaccountnumber,debitednameandaddress,creditedaccountnumber,creditednameandaddress,title,amount,status,data) VALUES ('"+BankNo+"', '"+PaymentSum+"', '"+DebitedAccountNumber+"', '"+DebitedNameAndAddress+"', '"+CreditedAccountNumber+"', '"+CreditedNameAndAddress+"', '"+Title+"', '"+Amount+"', '2','"+dataa+"');");
    const results = { 'results': (result) ? result.rows : null};
    res.send("wyslano");
    console.log(Amount);
    client.release();
    }catch(err){
    console.error(err);
    res.send("Error " + err);
    }

})

app.post("/send_first", async function(req,res){
  
  var bn = req.body.bn;

    try{
    const client = await pool.connect();
    const result = await client.query("INSERT INTO banki(bn,auth) VALUES ('22','ss')");
    const results = { 'results': (result) ? result.rows : null};
    res.send("wyslano");
    console.log(Amount);
    client.release();
    }catch(err){
    console.error(err);
    res.send("Error " + err);
    }

})


app.get('/get', async (req, res) => {
  var bank = req.query.id;
  try {
    const client = await pool.connect();
    const result = await client.query('SELECT * FROM przelewy WHERE bankno = '+bank+' AND status = 2');
    client.query('UPDATE przelewy SET status=1 WHERE bankno = '+bank+' AND status = 2');
    //update stanu
    res.send(result.rows);
    client.release();
  } catch (err) {
    console.error(err);
    res.send("Error " + err);
  }
})


app.listen(port)
