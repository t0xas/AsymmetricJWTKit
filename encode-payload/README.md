## Asymmetric JWT kits

This application generated and validated JWT token by asymmetric algorithm by x509 certificates chain.

Also you can easy validate apple webhook data.

### Description

You generate private certificate, then generate CA certificate by private.  

Then you CA certificate open to you users for 
verification JWT token

after users can validate you token by open CA root certificate

### Installation
```
 composer requiere azabolotnikov/asymmetric-jwt-kits
```

### Usage

For make JWT token
```
app(AsymmetricDataEncoder::class)->getJwt($data);
```

For check JWT token

```
app(AsymmetricDataEncoder::class)->checkJwt($data);
```
