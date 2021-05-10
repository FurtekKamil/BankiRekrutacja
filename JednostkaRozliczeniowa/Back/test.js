var express = require('express')
var bodyParser = require('body-parser')
var axios = require('axios')

var cookieParser = require('cookie-parser')

var dotenv = require('dotenv')
var jwt = require('jsonwebtoken')
dotenv.config()
var app = express()
var bcrypt = require('bcrypt')
const port = process.env.PORT || 3001


const { Pool } = require('pg');
const { registerHelper } = require('hbs')
const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: {
    rejectUnauthorized: false
  }
});


app.use(bodyParser.urlencoded({extended:false}));
app.use(bodyParser.json());
app.use(bodyParser.raw());
app.use(cookieParser())
app.use(express.json())
app.use(express.urlencoded({extended:true}))


app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin,debet, X-Requested-With, Content-Type, Accept");
  next();
});
/*
app.use(function(req, res, next){
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Credentials", "true");
  res.header("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT");
  res.header("Access-Control-Allow-Headers", "Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, debet, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");
  next();
});
*/
//eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNjEyNDcwMzI5fQ.56WfkfWjKZFo_M-E1PSZrQ_MbRDlRz8br1zRAFF-kOs

app.get('/get_token', autenticate, (req,res)=>{
  var nr=req.query.id_konta;

  if(nr == null){
    nr=1;
  }
  const accessToken = jwt.sign({id: nr},process.env.TOKEN_SECRET, {expiresIn: 360})
  const refreshToken = jwt.sign({id: nr},process.env.REFRESH_TOKEN_SECRET, {expiresIn: 800})
  var result = JSON.stringify({aToken: accessToken, rToken: refreshToken})
  res.send(result);
})

function autenticate (req,res,next){
  const authHeader = req.headers['debet']
  const token = authHeader && authHeader.split(' ')[1]

  if(token === null) return res.sendStatus(401)

  jwt.verify(token,process.env.TOKEN_SECRET, (err,user)=>{
      if(err) return res.sendStatus(403)

      req.user = user;
      next();
  })
}

app.post('/login', async (req,res)=>{
  var login = req.body.login;
  var haslo = req.body.haslo;

  const client = await pool.connect();
  const results = await client.query('SELECT * FROM employe WHERE login = '+login+'');
  if(results.rows.length>0){
  if(bcrypt.compareSync(haslo, results.rows[0].haslo)) {

    const accessToken = jwt.sign({id: login},process.env.TOKEN_SECRET, {expiresIn: 360})
    const refreshToken = jwt.sign({id: login},process.env.REFRESH_TOKEN_SECRET, {expiresIn: 800})
    var we = JSON.stringify({aToken: accessToken, rToken: refreshToken})

    res.cookie('JWT',accessToken,{
      maxAge:86400000,
      httpOnly:true,
    })

    res.send(we);

    } else {
     res.send("zlehaslo");
    }
  }else{
    res.send("niemauzytkownika");
  }
})



app.post("/send",autenticate, async function(req,res){
  
  var PaymentSum = req.body.PaymentSum;
  var DebitedAccountNumber = req.body.DebitedAccountNumber;
  var DebitedNameAndAddress = req.body.DebitedNameAndAddress;
  var CreditedAccountNumber = req.body.CreditedAccountNumber;
  var CreditedNameAndAddress = req.body.CreditedNameAndAddress;
  var Title = req.body.Title;
  var Amount = req.body.Amount;
  var check = req.body.check;

  if(check==null){
    check=0;
  }
  if(Amount<=0){
    res.send("Ujemna");
  }


  if(PaymentSum == null || DebitedAccountNumber=="" || DebitedAccountNumber.length!=32 || CreditedAccountNumber.length!=32 || DebitedAccountNumber=="" || Title == "" || Amount == null){
    res.send("Blad");
  }
  var v1=CreditedAccountNumber.substr(3,4);
  var v2=CreditedAccountNumber.substr(8,4);

  BankNo=v1+v2;

  var data = new Date();
  var dataa=data.getDate()+"-"+(data.getMonth()+1)+"-"+data.getFullYear()+" "+(data.getHours()+1)+":"+data.getMinutes()


    try{
    const client = await pool.connect();

    const www = await client.query("Select avg(amount) from przelewy where debitedaccountnumber='"+DebitedAccountNumber+"' ");
    var avg= www.rows[0].avg;
    if((Amount<1000 || (avg>=1000 && Amount<avg*2) || check == 1)){
    const result = await client.query("INSERT into przelewy(bankno,paymentsum,debitedaccountnumber,debitednameandaddress,creditedaccountnumber,creditednameandaddress,title,amount,status,data) VALUES ('"+BankNo+"', '"+PaymentSum+"', '"+DebitedAccountNumber+"', '"+DebitedNameAndAddress+"', '"+CreditedAccountNumber+"', '"+CreditedNameAndAddress+"', '"+Title+"', '"+Amount+"', '2','"+dataa+"');");
    const results = { 'results': (result) ? result.rows : null};
    res.send(avg);
    console.log(Amount);
    client.release();
  }else{
    const result = await client.query("INSERT into przelewy(bankno,paymentsum,debitedaccountnumber,debitednameandaddress,creditedaccountnumber,creditednameandaddress,title,amount,status,data) VALUES ('"+BankNo+"', '"+PaymentSum+"', '"+DebitedAccountNumber+"', '"+DebitedNameAndAddress+"', '"+CreditedAccountNumber+"', '"+CreditedNameAndAddress+"', '"+Title+"', '"+Amount+"', '3','"+dataa+"');");
    const results = { 'results': (result) ? result.rows : null};
    res.send(avg);
    console.log(Amount);
    client.release();
    }
    
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

app.get('/get_check', async (req, res) => {
  try {
    const client = await pool.connect();
    const result = await client.query('SELECT * FROM przelewy WHERE status = 3');
    res.send(result.rows);
    client.release();
  } catch (err) {
    console.error(err);
    res.send("Error " + err);
  }
})

app.get('/get_avg',async(req,res)=>{
  var DebitedAccountNumber=req.query.nr;
  try {
    const client = await pool.connect();
    const result = await client.query("Select avg(amount) from przelewy where debitedaccountnumber='"+DebitedAccountNumber+"' ");
    res.send(result.rows);
    client.release();
  } catch (err) {
    console.error(err);
    res.send("Error " + err);
  }
})

app.post('/accept',autenticate,async(req,res)=>{
  var id = req.body.id_payment;
  try {
    const client = await pool.connect();
    const result = await client.query("Update przelewy set status=2 where id_payment="+id+"");
    res.send(result.rows);
    client.release();
  } catch (err) {
    console.error(err);
    res.send("Error " + err);
  }
})

app.post('/decline',autenticate,async(req,res)=>{
  var id = req.body.id_payment;
  try {
    const client = await pool.connect();
    const result = await client.query("select * from przelewy where id_payment="+id+"");
    await client.query("update przelewy set status = -1 where id_payment="+id+"");
    client.release();

    var data={
      PaymentSum: result.rows[0].paymentsum,
      DebitedAccountNumber:result.rows[0].creditedaccountnumber,
      DebitedNameAndAddress:result.rows[0].creditednameandaddress,
      CreditedAccountNumber:result.rows[0].debitedaccountnumber,
      CreditedNameAndAddress:result.rows[0].debitednameandaddress,
      Amount: result.rows[0].amount,
      Title:result.rows[0].title,
      check:1
     }     

    axios
    .post('https://jednroz.herokuapp.com/send', data, {
        headers: {
            'debet': 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNjEyNDcwMzI5fQ.56WfkfWjKZFo_M-E1PSZrQ_MbRDlRz8br1zRAFF-kOs'
        }

    })
    .then((res) => {
        console.log(`statusCode: ${res.statusCode}`)
        console.log(res)
    })
    .catch((error) => {
        console.error(error)
    })


    res.send("Odrzucono");
  } catch (err) {
    console.error(err);
    res.send("Error " + err);
  }
})

/*
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

*/
app.listen(port)
