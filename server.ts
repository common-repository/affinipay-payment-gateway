import * as express from 'express';
import * as bodyParser from 'body-parser';

const app: express.Application = express();
const port: number = 3001;

app.use(bodyParser.json());

app.get('/users',  (req, res) => {
  const content = { merchants: [{"id":"7RbIOalFQ1-kQY9b8MD_SA","created":"2018-04-26T17:10:43.946Z","modified":"2018-06-02T04:11:52.436Z","status":"ACTIVE","trust_account":false,"name":"Test Account","primary":false,"currency":"USD","accepted_card_types":"VISA,MASTERCARD,AMERICAN_EXPRESS,DISCOVER","required_payment_fields":"","swipe_required_payment_fields":"","cvv_policy":"DISABLED","avs_policy":"DISABLED","ignore_avs_failure_if_cvv_match":true,"swipe_cvv_policy":"DISABLED","swipe_avs_policy":"DISABLED","swipe_ignore_avs_failure_if_cvv_match":false,"transaction_allowed_countries":""},{"id":"G0Y5h6W-TYG026YcQAyVTg","created":"2017-05-08T19:34:09.653Z","modified":"2018-06-01T04:01:34.121Z","status":"ACTIVE","trust_account":false,"name":"the new account name","primary":true,"currency":"USD","accepted_card_types":"VISA,MASTERCARD,AMERICAN_EXPRESS,DISCOVER","required_payment_fields":"cvv,name,address1,city,state,postal_code","swipe_required_payment_fields":"cvv,name,email,address1,city,state,postal_code,country","cvv_policy":"DISABLED","avs_policy":"DISABLED","ignore_avs_failure_if_cvv_match":true,"swipe_cvv_policy":"DISABLED","swipe_avs_policy":"DISABLED","swipe_ignore_avs_failure_if_cvv_match":false,"transaction_allowed_countries":""}]};
  res.json(content);
});

app.post('/wp-json/affinipay/v1/charge', (req,res) =>{
  console.log(req.body)


  //res.json({"attributes":{"id":"NUlEBh8rS5-G0WVsaR6GaA","created":"2018-06-14T16:47:28.025Z","modified":"2018-06-14T16:47:28.073Z","account_id":"7RbIOalFQ1-kQY9b8MD_SA","status":"AUTHORIZED","auto_capture":true,"amount":115,"currency":"USD","authorization_code":"POKVVJ","amount_refunded":0,"type":"CHARGE","method":{"name":"Joe","email":"jjungmann@affinipay.com","type":"card","number":"************4242","fingerprint":"GunPelYVthifNV63LEw1","card_type":"VISA","exp_month":8,"exp_year":2019},"avs_result":"ADDRESS_AND_POSTAL_CODE"}});
  //return;

  //{"code":"Error","message":"","data":{"status":403,"messages":[{"context":"method","code":"invalid_data","sub_code":"not_null","level":"error","message":"The value is not valid","facility":"gateway"},{"context":"amount","code":"invalid_data","sub_code":"below_minimum_value","level":"error","message":"Amount is less than the minimum value","facility":"gateway"}]}}
  res.status(403);
  res.json({
    "code":"Error",
    data: {
    "messages":[{
      "context":"method.exp_year",
      "code":"invalid_data",
      "sub_code":"not_null",
      "level":"error",
      "message":"Expiration year cannot be blank","facility":"gateway"
    },{
      "context":"method.exp_month",
      "code":"invalid_data",
      "sub_code":"not_null",
      "level":"error",
      "message":"Expiration month cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.postal_code",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"Postal code cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.city",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"City cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.state",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"State or province cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.number",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"Card number cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.name",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"Name cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.cvv",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"Card code cannot be blank",
      "facility":"gateway"
    },{
      "context":"method.address1",
      "code":"invalid_data",
      "sub_code":"not_blank",
      "level":"error",
      "message":"Street number cannot be blank",
      "facility":"gateway"
    }],
      "errors":[
        "Expiration year cannot be blank",
        "Expiration month cannot be blank",
        "Postal code cannot be blank",
        "City cannot be blank",
        "State or province cannot be blank",
        "Card number cannot be blank",
        "Name cannot be blank",
        "Card code cannot be blank",
        "Street number cannot be blank"
      ]}});
});

app.post('/wp-json/affinipay/v1/save', (req,res) =>{
  res.status(403);
  res.json({"code":"authentication_error","message":"Invalid credentials","data":{"status":403}});
  return;

  res.json([
      {
        id: "7RbIOalFQ1-kQY9b8MD_SA",
        created: "2018-04-26T17:10:43.946Z",
        modified: "2018-06-07T04:09:52.506Z",
        status: "ACTIVE",
        trust_account: false,
        name: "Test Account",
        primary: false,
        currency: "USD",
        accepted_card_types: "VISA,MASTERCARD,AMERICAN_EXPRESS,DISCOVER",
        required_payment_fields: "",
        swipe_required_payment_fields: "",
        cvv_policy: "DISABLED",
        avs_policy: "DISABLED",
        ignore_avs_failure_if_cvv_match: true,
        swipe_cvv_policy: "DISABLED",
        swipe_avs_policy: "DISABLED",
        swipe_ignore_avs_failure_if_cvv_match: false,
        transaction_allowed_countries: ""
      },
      {
        id: "G0Y5h6W-TYG026YcQAyVTg",
        created: "2017-05-08T19:34:09.653Z",
        modified: "2018-06-07T11:00:03.899Z",
        status: "ACTIVE",
        trust_account: false,
        name: "the new account name",
        primary: true,
        currency: "USD",
        accepted_card_types: "VISA,MASTERCARD,AMERICAN_EXPRESS,DISCOVER",
        required_payment_fields: "cvv,name,address1,city,state,postal_code",
        swipe_required_payment_fields:
          "cvv,name,email,address1,city,state,postal_code,country",
        cvv_policy: "DISABLED",
        avs_policy: "DISABLED",
        ignore_avs_failure_if_cvv_match: true,
        swipe_cvv_policy: "DISABLED",
        swipe_avs_policy: "DISABLED",
        swipe_ignore_avs_failure_if_cvv_match: false,
        transaction_allowed_countries: ""
      }
    ]);
});

app.listen(port, () => {
    global.console.log(`Listening at http://localhost:${port}/`);
});