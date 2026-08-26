<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Privacy — Budget Pilot</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/style.css" />
</head>
<body>

<header class="topbar" id="topbar">
  <div class="topbar__inner">
    <a class="brand" href="<?php echo URLROOT; ?>/customer/index">
      <span class="brand__mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="currentColor" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="currentColor"/></svg>
      </span>
      <span class="brand__text">
        <span class="brand__name">Budget Pilot</span>
        <span class="brand__tag">Smart finance copilot</span>
      </span>
    </a>

    <nav class="topnav" aria-label="Sections">
      <a href="<?php echo URLROOT; ?>/#how">How it works</a>
      <a href="<?php echo URLROOT; ?>/#features">What you get</a>
      <a href="<?php echo URLROOT; ?>/customer/terms">Terms</a>
    </nav>

    <div class="topbar__actions">
      <a class="btn btn--quiet" href="<?php echo URLROOT; ?>/customer/login">Sign in</a>
      <a class="btn btn--lime" href="<?php echo URLROOT; ?>/customer/register">Create account</a>
    </div>
  </div>
</header>

<main class="doc">
  <div class="doc__head">
    <p class="eyebrow eyebrow--dark">Privacy</p>
    <h1 class="doc__title">What Budget Pilot does with your information</h1>
    <p class="doc__lede">
      Short version: your budget, expenses and shopping history are saved in your own
      browser on your own device. There is no Budget Pilot server holding them, and
      nothing is sold or handed to advertisers.
    </p>
    <p class="doc__stamp">Last updated 15 August 2026</p>
  </div>

  <div class="doc__body">

    <section class="doc__section">
      <h2>1. Who this covers</h2>
      <p>
        This page describes Budget Pilot, a coursework project built as a web application.
        "You" means anyone signed in to it, whether as an account holder or as a family
        member added to someone else's account.
      </p>
    </section>

    <section class="doc__section">
      <h2>2. What is stored</h2>
      <p>When you use Budget Pilot, the following is written to your browser's storage:</p>
      <ul class="doc__list">
        <li>Account details you type in: name, email address, gender, age, NIC, and any profile photo you upload.</li>
        <li>Money figures you enter: your monthly income and savings balance.</li>
        <li>Your budget: the amount planned for each category and the amount spent against it.</li>
        <li>Every expense you log, including its category, amount, date, description and notes.</li>
        <li>Your shopping cart and the orders you have checked out, item by item.</li>
        <li>Preferences such as currency, theme and whether alerts are switched on.</li>
        <li>Which alerts you have read or dismissed.</li>
      </ul>
      <p>
        Passwords are not kept as plain text. They are put through a simple one-way hash
        first. Be aware that this hash is obfuscation, not real cryptographic protection —
        see section 6.
      </p>
    </section>

    <section class="doc__section">
      <h2>3. Where it is stored</h2>
      <p>
        All of it lives in <span class="doc__code">localStorage</span> in the browser you
        are using. It does not travel over the internet, because Budget Pilot has no back
        end to send it to. Two consequences follow, and both matter:
      </p>
      <ul class="doc__list">
        <li>
          Anyone who can use your device and browser profile can open Budget Pilot and read
          this data, including your saved figures. A sign-in prompt does not stop them,
          because the data sits in the browser either way.
        </li>
        <li>
          Clearing your browsing data, using private browsing, or switching to another
          browser or device means the account and everything in it is gone. There is no
          backup and no way for anyone to recover it.
        </li>
      </ul>
    </section>

    <section class="doc__section">
      <h2>4. What other people on your account can see</h2>
      <p>
        An account can hold several people: the person who signed up is the account holder,
        and anyone they add is a member. They share one budget, which changes who sees what:
      </p>
      <ul class="doc__list">
        <li>
          <strong>The account holder sees every transaction</strong> logged by anyone on the
          account, with the name of the person who logged it, on the Household page.
        </li>
        <li>
          <strong>A member sees only their own transactions.</strong> They do not see what
          other members have bought or logged.
        </li>
        <li>
          <strong>Everyone sees the shared budget totals</strong> — what is planned for each
          category and how much of it is left — because everyone is spending from the same
          money. So while a member's individual purchases stay private from other members,
          the effect of those purchases on the household totals is visible to all of them.
        </li>
      </ul>
      <p>
        If you would rather your spending were not visible to the account holder, do not
        join their account — create your own instead.
      </p>
    </section>

    <section class="doc__section">
      <h2>5. What is not done with your information</h2>
      <ul class="doc__list">
        <li>It is not sold, rented or shared with advertisers.</li>
        <li>It is not sent to the brands or suppliers whose prices you compare.</li>
        <li>There is no tracking pixel, analytics script or advertising cookie on any page.</li>
        <li>No marketing email is sent. The subscribe box on the home page is a demo control and stores nothing.</li>
      </ul>
      <p>
        The one thing loaded from outside is the web font used across the site, which is
        requested from Google Fonts. That request tells Google your IP address, as any
        request to any website does. Nothing about your budget is included in it.
      </p>
    </section>

    <section class="doc__section">
      <h2>6. How protected this actually is</h2>
      <p>
        Budget Pilot is a student project, and it would be dishonest to describe it as
        bank-grade. Specifically: sign-in is checked in the browser rather than on a server,
        password hashing uses a fast non-cryptographic function, and nothing is encrypted at
        rest. Someone with access to your unlocked device could read the stored data
        directly through the browser's developer tools.
      </p>
      <p>
        Use figures you are comfortable having on your own machine at that level of
        protection. Do not enter real bank credentials, card numbers or account numbers —
        Budget Pilot never asks for them and has no field for them.
      </p>
    </section>

    <section class="doc__section">
      <h2>7. Deleting your information</h2>
      <p>
        You are in control of all of it. On the Settings page you can edit your details or
        remove a profile from the account. To erase everything at once, clear site data for
        Budget Pilot in your browser settings — that removes every profile, budget, expense
        and order permanently, with no copy left anywhere.
      </p>
    </section>

    <section class="doc__section">
      <h2>8. Children</h2>
      <p>
        Account creation asks for an age of 13 or over. If you add a family member under
        that age, you are responsible for what they enter and for supervising their use of
        the account.
      </p>
    </section>

    <section class="doc__section">
      <h2>9. Changes and contact</h2>
      <p>
        If this page changes, the date at the top changes with it. For questions about the
        project or this policy, contact the account holder who deployed it — as coursework,
        Budget Pilot has no support desk of its own.
      </p>
    </section>

    <p class="doc__foot">
      See also the <a href="<?php echo URLROOT; ?>/customer/terms">Terms of Service</a>.
    </p>
  </div>
</main>

<footer class="footer">
  <div class="footer__bottom footer__bottom--solo">
    <p>© 2026 Budget Pilot. Coursework project.</p>
    <ul class="footer__legal">
      <li><a href="<?php echo URLROOT; ?>/customer/index">Home</a></li>
      <li><a href="<?php echo URLROOT; ?>/customer/privacy">Privacy</a></li>
      <li><a href="<?php echo URLROOT; ?>/customer/terms">Terms</a></li>
    </ul>
  </div>
</footer>

<script src="<?php echo URLROOT; ?>/public/js/customer-js/main.js"></script>
</body>
</html>
