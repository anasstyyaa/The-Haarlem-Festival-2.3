<?php /** @var string $link */ ?>
<div style="font-family: Arial, sans-serif; background-color: #f5e6d3; padding: 20px;">
  
  <div style="max-width: 500px; margin: auto; background: #ffffff; padding: 25px; border-radius: 10px; text-align: center;">
    
    <h2 style="color: #6b3e26;">Haarlem Festival</h2>

    <p style="color: #333; font-size: 16px;">
      You requested to reset your password.
    </p>

    <p style="color: #555;">
      Click the button below to reset your password:
    </p>

    <a href="<?= htmlspecialchars($link) ?>" 
       style="
         display: inline-block;
         padding: 12px 20px;
         margin-top: 15px;
         background-color: #a47148;
         color: #ffffff;
         text-decoration: none;
         border-radius: 6px;
         font-weight: bold;
       ">
       Reset Password
    </a>

    <p style="margin-top: 15px; font-size: 12px;">
      Or copy this link:
      <br>
      <a href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($link) ?></a>
    </p>

    <p style="margin-top: 20px; font-size: 12px; color: #777;">
      This link will expire in 30 minutes.
    </p>

    <p style="margin-top: 10px; font-size: 12px; color: #777;">
      If you did not request this, you can ignore this email.
    </p>

  </div>

</div>