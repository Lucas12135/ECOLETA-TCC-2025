<?php
return [
  'host' => 'smtp-relay.brevo.com',
  'username' => '9a7d1c001@smtp-brevo.com', // seu login SMTP
  'password' => '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', // sua SMTP key (API key)
  'port' => 587,     // porta TLS padrão
  'from_email' => 'ecoleta3@gmail.com', // ou o remetente verificado na Brevo
  'from_name' => 'Ecoleta',
  'secure' => 'tls'  // pode usar 'ssl' se preferir a porta 465
];
