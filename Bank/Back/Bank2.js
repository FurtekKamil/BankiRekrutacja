var express = require('express')
var bodyParser = require('body-parser')
 
var app = express()

const port = process.env.PORT || 3001

const { Pool } = require('pg');
const pool = new Pool({
  connectionString: process.env.DATABASE_URL,
  ssl: {
    rejectUnauthorized: false
  }
});


const schedule = require('node-schedule');

const https = require('https')

function get_payment() {
const get_payment = {
  hostname: 'bankio.herokuapp.com',
  port: 443,
  path: '/get_payment',
  method: 'GET'
}

const req = https.request(get_payment, res => {
  console.log(`statusCode: ${res.statusCode}`)

  res.on('data', d => {
    process.stdout.write(d)
  })
})

req.on('error', error => {
  console.error(error)
})
req.end()
return new Promise(resolve => {
  setTimeout(() => {
    resolve('resolved');
  }, 2000);
});
}


function check(){
  const check = {
    hostname: 'bankio.herokuapp.com',
    port: 443,
    path: '/check',
    method: 'GET'
  }
  
  const req_check = https.request(check, res => {
    console.log(`statusCode: ${res.statusCode}`)
  
    res.on('data', d => {
      process.stdout.write(d)
    })
  })
  
  req_check.on('error', error => {
    console.error(error)
  })
  
  req_check.end()
  return 1;
}



schedule.scheduleJob('15 9,11,15 * * 1-5', function(){
  get_payment();
});

schedule.scheduleJob('17 9,11,15 * * 1-5', function(){
  check();
});



app.use(bodyParser.urlencoded({extended:false}));
app.use(bodyParser.json());
app.use(bodyParser.raw());

app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
});

app.get('/get_payment', async (req, res) => {

    fetch = require('node-fetch');
    const client = await pool.connect();
    let url = "https://jednroz.herokuapp.com/get?id=12402379";

    let settings = { method: "Get" };

    fetch(url, settings)
        .then(res => res.json())
        .then((json) => {
            
            json.forEach(element => {
                    client.query("INSERT INTO historia (paymentsum, nr_nadawcy, nazwa_nadawcy, nr_odbiorcy, nazwa_odbiorcy, tytul, kwota, id_platnosci_jedn, status, data) VALUES ('"+element['paymentsum']+"', '"+element['debitedaccountnumber']+"', '"+element['debitednameandaddress']+"', '"+element['creditedaccountnumber']+"', '"+element['creditednameandaddress']+"', '"+element['title']+"', '"+element['amount']+"', '"+element['id_payment']+"', '4', '"+element['data']+"');");
            });
            client.release();
            res.send(json);
        });

})

app.post('/login', async (req, res) => {
    var login = req.body.login;
    var haslo = req.body.haslo;
    const client = await pool.connect();
    var result = await client.query("select * from clients join passwd on clients.id_klienta=passwd.id_klienta join konta on clients.id_klienta=konta.id_klienta WHERE login='"+login+"' AND haslo='"+haslo+"';");
    if(result.rows.length==0){
        res.send("Notfound");
    }

    const fetch = require('node-fetch');

    let url = "https://jednroz.herokuapp.com/get_token";

    let settings = { method: "Get",headers:{'debet': 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MSwiaWF0IjoxNjEyNDcwMzI5fQ.56WfkfWjKZFo_M-E1PSZrQ_MbRDlRz8br1zRAFF-kOs'} };

    fetch(url, settings)
        .then(res => res.json())
        .then((json) => {
            var w = JSON.stringify({aToken: json.aToken,rToken:json.rToken,id_klienta:result.rows[0].id_klienta,nr: result.rows[0].nr})
            var result_json = JSON.parse(w);
            res.send(result_json);
        
        });
    //res.send(result.rows);

})



app.get('/check', async (req, res) => {

    const client = await pool.connect();
    const axios = require('axios')
    var result = await client.query("Select * from historia where status = 4 ");
    var d='';
    for(var i=0;i<result.rowCount;i++)
    {
        var resultw = await client.query("Select nr from konta where nr='"+result.rows[i].nr_odbiorcy+"' ");
        if(resultw.rows.length==0){

            d='Nie ma numeru, powrot do jednostki';
    
            var PaymentSum = result.rows[i].paymentsum;
            var DebitedAccountNumber = result.rows[i].nr_odbiorcy;
            var DebitedNameAndAddress = result.rows[i].nazwa_odbiorcy;
            var CreditedAccountNumber = result.rows[i].nr_nadawcy;
            var CreditedNameAndAddress = result.rows[i].nazwa_nadawcy;
            var Title = result.rows[i].tytul+" ZWROT";
            var Amount = result.rows[i].kwota;

            axios
            .post('https://jednroz.herokuapp.com/send', {
                PaymentSum: PaymentSum,
                DebitedAccountNumber:DebitedAccountNumber,
                DebitedNameAndAddress:DebitedNameAndAddress,
                CreditedAccountNumber:CreditedAccountNumber,
                CreditedNameAndAddress:CreditedNameAndAddress,
                Amount: Amount,
                Title:Title
            })
            .then((res) => {
                console.log(`statusCode: ${res.statusCode}`)
                console.log(res)
            })
            .catch((error) => {
                console.error(error)
            })



        }else{
            client.query("Update konta set saldo=saldo+"+result.rows[i].kwota+" WHERE nr='"+result.rows[i].nr_odbiorcy+"';");
            d='Jest Numer,dopisano kwote do salda';
            
        }

    } 
    client.query("Update historia set status=10 where status=4")  
    res.send(d);
    
    /*
    result.rows.forEach(async element=>{
        var resultw = await client.query("Select nr from konta where n="+element.nr_odbiorcy+" ");
        res.send(resultw);


    })
     */   
})

function getRandomInt() {
    min = Math.ceil(1000);
    max = Math.floor(9999);
    return Math.floor(Math.random() * (max - min)) + min;
}

function calculateSK(bn,n){
    var cnumber='252100';

    bn = bn.replace(/\s/g, '');

    n = n.replace(/\s/g, '');

    var c1=bn%97;

    var c2=(c1+n.substr(0,8))%97;

    c1=(c2+n.substr(8,8))%97;

    c2=98-((c1+cnumber)%97);

    if(c2<10){
        c2="0"+c2;
    }

    return c2;
}

app.post('/register', async (req, res) => {
    try{
        const client = await pool.connect();
        var imie = req.body.imie;
        var nazwisko = req.body.nazwisko;
        var adres = req.body.adres;
        var kod_pocztowy = req.body.kod_pocztowy;
        var miejscowosc = req.body.miejscowosc;
        var pesel = req.body.pesel;
        var telefon = req.body.telefon;
        var login = req.body.login;
        var haslo = req.body.haslo;

        var nrbanku='1240 2379'
        var mod=Number(pesel%11)+1000;
        var nrkonta=getRandomInt()+" "+getRandomInt()+" "+getRandomInt()+" "+getRandomInt();

        var sk=calculateSK(nrbanku,nrkonta);

        var calosc= sk+" "+nrbanku+" "+nrkonta;
        
        client.query("INSERT INTO clients (id_klienta, imie, nazwisko, adres, kod_pocztowy, miejscowosc, pesel, telefon) VALUES (nextval('w'),'"+imie+"', '"+nazwisko+"', '"+adres+"', '"+kod_pocztowy+"', '"+miejscowosc+"', '"+pesel+"', '"+telefon+"');");
        client.query("INSERT INTO konta (saldo, nr, id_klienta) VALUES ('0', '"+calosc+"', currval('w'));")
        client.query("INSERT INTO passwd(login,haslo,id_klienta) VALUES ('"+login+"','"+haslo+"',currval('w'))")

        res.send('Dodano');
        client.release();


    }catch{
        res.send("blad");
    }
   
})

app.post('/add_account', async (req,res)=>{
    const client = await pool.connect();

    var nrbanku='1240 2379'
    var nrkonta=getRandomInt()+" "+getRandomInt()+" "+getRandomInt()+" "+getRandomInt();

    var sk=calculateSK(nrbanku,nrkonta);

    var calosc= sk+" "+nrbanku+" "+nrkonta;

    var id = req.body.id;
    client.query("INSERT INTO konta (saldo, nr, id_klienta) VALUES ('0', '"+calosc+"', "+id+");");
    res.send("dodano");
    client.release();
})

app.get('/get_history', async (req, res) => {
    var id=req.query.nr_konta;
    try{
        const client = await pool.connect();
        const result = await client.query("Select * from historia where nr_odbiorcy='"+id+"' or nr_nadawcy='"+id+"';");
        res.send(result.rows);
        client.release();
    }catch(err){
        console.error(err);
        res.send("Error " + err);
    }

})

app.get('/get_history_check', async (req, res) => {
    var id=req.query.nr_konta;
    try{
        const client = await pool.connect();
        const result = await client.query("Select * from historia where nr_nadawcy='"+id+"' and status=7;");
        res.send(result.rows);
        client.release();
    }catch(err){
        console.error(err);
        res.send("Error " + err);
    }

})

app.get("/dane",async (req,res) => {
    var nr_konta = req.query.nr_konta;
    const client = await pool.connect();
    
    var result = await client.query("select * from clients join konta on clients.id_klienta=konta.id_klienta where nr='"+nr_konta+"';")
    res.send(result.rows);
    client.release();

});

app.get("/dane_konta",async(req,res)=>{
    var id = req.query.id;
    const client = await pool.connect();
    var result = await client.query(" select * from konta join clients on konta.id_klienta=clients.id_klienta where clients.id_klienta="+id+";")
    res.send(result.rows);
    client.release();

});

app.post('/send_normal', async (req, res) => {
    try{
    const client = await pool.connect();
    const axios = require('axios')
    
    var PaymentSum = req.body.PaymentSum;
    var DebitedAccountNumber = req.body.DebitedAccountNumber;
    var DebitedNameAndAddress = req.body.DebitedNameAndAddress;
    var CreditedAccountNumber = req.body.CreditedAccountNumber;
    var CreditedNameAndAddress = req.body.CreditedNameAndAddress;
    var Title = req.body.Title;
    var Amount = req.body.Amount;
    var check=req.body.check;
    var auth=req.body.auth;

    var v1=CreditedAccountNumber.substr(3,4);
    var v2=CreditedAccountNumber.substr(8,4);

    const result = await client.query("Select avg(kwota) from historia where nr_nadawcy='"+DebitedAccountNumber+"' ");
    var avg= result.rows[0].avg;
    BankNo=v1+v2;
    var data = new Date();
    var dataa=data.getDate()+"-"+(data.getMonth()+1)+"-"+data.getFullYear()+" "+(data.getHours()+1)+":"+data.getMinutes()
    if(Amount<=0){
        res.send("Ujemna");
    }

            client.query("delete from historia where status=7");
    if((Amount<1000 || (avg>=1000 && Amount<avg*2)) || check==1){
        if(BankNo=="12402379"){

            client.query("Update konta set saldo=saldo+"+Amount+" WHERE nr='"+CreditedAccountNumber+"';");
            client.query("Update konta set saldo=saldo-"+Amount+" WHERE nr='"+DebitedAccountNumber+"';");
            client.query("INSERT INTO historia (paymentsum, nr_nadawcy, nazwa_nadawcy, nr_odbiorcy, nazwa_odbiorcy, tytul, kwota, id_platnosci_jedn, status, data) VALUES ('"+PaymentSum+"', '"+DebitedAccountNumber+"', '"+DebitedNameAndAddress+"', '"+CreditedAccountNumber+"', '"+CreditedNameAndAddress+"', '"+Title+"', '"+Amount+"', '0', '0', '"+dataa+"');");
            
            res.send("Wewnetrzny");

        }else{
        
       var data={
        PaymentSum: PaymentSum,
        DebitedAccountNumber:DebitedAccountNumber,
        DebitedNameAndAddress:DebitedNameAndAddress,
        CreditedAccountNumber:CreditedAccountNumber,
        CreditedNameAndAddress:CreditedNameAndAddress,
        Amount: Amount,
        Title:Title
       }     

        axios
        .post('https://jednroz.herokuapp.com/send', data, {
            headers: {
                'debet': 'Bearer '+auth
            }

        })
        .then((res) => {
            console.log(`statusCode: ${res.statusCode}`)
            console.log(res)
        })
        .catch((error) => {
            console.error(error)
        })

        client.query("INSERT INTO historia (paymentsum, nr_nadawcy, nazwa_nadawcy, nr_odbiorcy, nazwa_odbiorcy, tytul, kwota, id_platnosci_jedn, status, data) VALUES ('"+PaymentSum+"', '"+DebitedAccountNumber+"', '"+DebitedNameAndAddress+"', '"+CreditedAccountNumber+"', '"+CreditedNameAndAddress+"', '"+Title+"', '"+Amount+"', '0', '0', '"+dataa+"');");
        client.query("Update konta set saldo=saldo-"+Amount+" WHERE nr='"+DebitedAccountNumber+"';");
        res.send("Zewnetrzny");
        }
    
    }else{
        res.send("0");
    }
    }catch{
        res.send("PROBLEM");
    }
    
})

app.post('/set_to_check', async (req, res) => {
    
    const client = await pool.connect();

    var PaymentSum = req.body.PaymentSum;
    var DebitedAccountNumber = req.body.DebitedAccountNumber;
    var DebitedNameAndAddress = req.body.DebitedNameAndAddress;
    var CreditedAccountNumber = req.body.CreditedAccountNumber;
    var CreditedNameAndAddress = req.body.CreditedNameAndAddress;
    var Title = req.body.Title;
    var Amount = req.body.Amount;

    var data = new Date();
    var dataa=data.getDate()+"-"+(data.getMonth()+1)+"-"+data.getFullYear()+" "+(data.getHours()+1)+":"+data.getMinutes()

    client.query("INSERT INTO historia (paymentsum, nr_nadawcy, nazwa_nadawcy, nr_odbiorcy, nazwa_odbiorcy, tytul, kwota, id_platnosci_jedn, status, data) VALUES ('"+PaymentSum+"', '"+DebitedAccountNumber+"', '"+DebitedNameAndAddress+"', '"+CreditedAccountNumber+"', '"+CreditedNameAndAddress+"', '"+Title+"', '"+Amount+"', '0', '7', '"+dataa+"');");

    res.send(Amount);

})

app.listen(port)
