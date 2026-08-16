<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Budget Pilot — know what's left before you spend it</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/customer-css/style.css" />
</head>
<body>

<!-- ============ TOP BAR ============ -->
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
      <a href="#how">How it works</a>
      <a href="#features">What you get</a>
      <a href="#security">Security</a>
    </nav>

    <div class="topbar__actions">
      <a class="btn btn--quiet" href="<?php echo URLROOT; ?>/customer/login">Sign in</a>
      <a class="btn btn--lime" href="<?php echo URLROOT; ?>/customer/register">Create account</a>
    </div>
  </div>
</header>

<main>

  <!-- ============ HERO ============ -->
  <section class="hero">
    <div class="hero__inner">

      <div class="hero__copy">
        <p class="eyebrow">Budgets · Groceries · One app</p>

        <h1 class="hero__title">
          Know what's left<br />
          <em>before</em> you spend it.
        </h1>

        <p class="hero__text">
          Budget Pilot splits your income across the things you actually pay for, tracks
          every expense against it, and checks the grocery catalog for a cheaper brand of
          the same thing before you reach the till.
        </p>

        <div class="hero__actions">
          <a class="btn btn--lime btn--lg" href="<?php echo URLROOT; ?>/customer/register">Start your budget</a>
          <a class="btn btn--outline btn--lg" href="<?php echo URLROOT; ?>/customer/login">I already have an account</a>
        </div>

        <dl class="proof">
          <div class="proof__item">
            <dt>Categories tracked</dt>
            <dd>8</dd>
          </div>
          <div class="proof__item">
            <dt>Products compared</dt>
            <dd>60</dd>
          </div>
          <div class="proof__item">
            <dt>Kept as a cushion</dt>
            <dd>6%</dd>
          </div>
        </dl>
      </div>

      <!-- ---- signature: income arriving, then splitting into categories ---- -->
      <div class="hero__art">
        <figure class="splitter" id="splitter">
          <figcaption class="splitter__head">
            <span class="splitter__label">Monthly income</span>
            <span class="splitter__income" id="splitterIncome">$2,000</span>
          </figcaption>

          <div class="splitter__source" aria-hidden="true">
            <span class="splitter__drain" id="splitterDrain"></span>
          </div>

          <ul class="slots" id="slots">
            <li class="slot" data-key="loan" style="--w:33">
              <span class="slot__name">Loan</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">33%</span>
            </li>
            <li class="slot" data-key="grocery" style="--w:15">
              <span class="slot__name">Grocery</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">15%</span>
            </li>
            <li class="slot" data-key="rent" style="--w:13">
              <span class="slot__name">Rent</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">13%</span>
            </li>
            <li class="slot" data-key="transport" style="--w:10">
              <span class="slot__name">Transport</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">10%</span>
            </li>
            <li class="slot" data-key="tuition" style="--w:8">
              <span class="slot__name">Tuition</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">8%</span>
            </li>
            <li class="slot" data-key="medicine" style="--w:5">
              <span class="slot__name">Medicine</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">5%</span>
            </li>
            <li class="slot" data-key="clothing" style="--w:5">
              <span class="slot__name">Clothing</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">5%</span>
            </li>
            <li class="slot" data-key="other" style="--w:5">
              <span class="slot__name">Other</span>
              <span class="slot__fill"></span>
              <span class="slot__pct">5%</span>
            </li>
          </ul>

          <p class="splitter__buffer" id="splitterBuffer">
            <span class="splitter__bufferlabel">Left as a cushion</span>
            <span class="splitter__buffervalue">6% · $120</span>
          </p>

          <button class="splitter__replay" type="button" id="splitterReplay">Split it again</button>
        </figure>
      </div>
    </div>
  </section>

  <!-- ============ HOW IT WORKS ============ -->
  <section class="how" id="how">
    <header class="section-head">
      <p class="eyebrow eyebrow--dark">Three steps, in order</p>
      <h2 class="section-head__title">Set it once, then just shop</h2>
    </header>

    <ol class="steps">
      <li class="step reveal">
        <span class="step__num">01</span>
        <h3 class="step__title">Split your income</h3>
        <p class="step__text">
          Enter what you earn. Budget Pilot suggests an amount for rent, loans, groceries,
          transport, tuition, medicine, clothing and everything else — and leaves a small
          cushion untouched. Change any figure you disagree with.
        </p>
      </li>
      <li class="step reveal">
        <span class="step__num">02</span>
        <h3 class="step__title">Log what you spend</h3>
        <p class="step__text">
          Add an expense in a few seconds, or check out a grocery cart. Either way the
          amount comes off that category, so the number you see is the number you have
          left.
        </p>
      </li>
      <li class="step reveal">
        <span class="step__num">03</span>
        <h3 class="step__title">Get told before it hurts</h3>
        <p class="step__text">
          At 80% of a category you get a warning. Past the limit you get an alert with the
          exact overspend. Cheaper brands and rollovers land in the same place.
        </p>
      </li>
    </ol>
  </section>

  <!-- ============ FEATURES ============ -->
  <section class="features" id="features">
    <header class="section-head">
      <p class="eyebrow eyebrow--dark">What you get</p>
      <h2 class="section-head__title">Everything in one place</h2>
      <p class="section-head__sub">No spreadsheets, no receipts in a shoebox.</p>
    </header>

    <div class="bento">
      <article class="fcard fcard--wide reveal">
        <span class="tile" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/><circle cx="17" cy="14.5" r="1.6"/>
          </svg>
        </span>
        <h3 class="fcard__title">Budgets that hold their shape</h3>
        <p class="fcard__text">
          Every category shows planned, spent and remaining. Spend against one and only
          that one moves.
        </p>

        <ul class="minibars" aria-hidden="true">
          <li><span class="minibars__label">Rent</span><span class="minibars__track"><i style="--w:64%"></i></span><span class="minibars__num">64%</span></li>
          <li><span class="minibars__label">Loan</span><span class="minibars__track"><i style="--w:41%"></i></span><span class="minibars__num">41%</span></li>
          <li><span class="minibars__label">Grocery</span><span class="minibars__track minibars__track--warn"><i style="--w:88%"></i></span><span class="minibars__num">88%</span></li>
          <li><span class="minibars__label">Transport</span><span class="minibars__track"><i style="--w:27%"></i></span><span class="minibars__num">27%</span></li>
        </ul>
      </article>

      <article class="fcard reveal">
        <span class="tile tile--mint" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/>
            <path d="M2.5 3h2.2l2.6 12.4a1.6 1.6 0 0 0 1.6 1.3h8.9a1.6 1.6 0 0 0 1.6-1.3L21 7H6"/>
          </svg>
        </span>
        <h3 class="fcard__title">Same product, every brand</h3>
        <p class="fcard__text">
          Each item lists the brands stocking it with the price per kg or litre worked out,
          so the cheapest one is obvious.
        </p>

        <div class="compare" aria-hidden="true">
          <div class="compare__row compare__row--win">
            <span>PureGrain 5kg</span><span class="compare__unit">$3.70/kg</span>
          </div>
          <div class="compare__row">
            <span>GoldenHarvest 5kg</span><span class="compare__unit">$4.59/kg</span>
          </div>
        </div>
      </article>

      <article class="fcard fcard--dark reveal">
        <span class="tile tile--ghost" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
            <path d="M6 20V11M12 20V5M18 20v-6"/>
          </svg>
        </span>
        <h3 class="fcard__title fcard__title--light">Where the month went</h3>
        <p class="fcard__text fcard__text--light">
          A monthly report with spend against budget, your top categories, the brands you
          buy most, and a CSV export.
        </p>
        <div class="bars" aria-hidden="true">
          <span class="bars__bar" style="--h:34px;--c:#2C3A7A"></span>
          <span class="bars__bar" style="--h:48px;--c:#4A57A0"></span>
          <span class="bars__bar" style="--h:62px;--c:#8E96C8"></span>
          <span class="bars__bar" style="--h:76px;--c:#9BEE95"></span>
        </div>
      </article>

      <article class="fcard fcard--wide reveal" id="security">
        <span class="tile" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l7 3v6c0 4.6-3 8-7 9-4-1-7-4.4-7-9V6z"/><path d="M9.5 12l1.8 1.8L15 10"/>
          </svg>
        </span>
        <h3 class="fcard__title">Your money stays your business</h3>
        <p class="fcard__text">
          Your budget, expenses and shopping history stay in your own browser. Nothing is
          sold on, and nothing is shared with the brands you compare.
        </p>
        <p class="fcard__fine">
          Budget Pilot is a coursework project — sign-in is stored on your device, so treat
          it as a demo rather than a bank.
        </p>
      </article>
    </div>
  </section>

  <!-- ============ CTA ============ -->
  <section class="cta" id="signin">
    <div class="cta__card">
      <p class="eyebrow">Pick your door</p>
      <h2 class="cta__title">Ready to take off?</h2>
      <p class="cta__text">
        Customers budget and shop. Suppliers list products and keep their prices current.
      </p>
      <div class="cta__actions">
        <a class="btn btn--lime btn--lg" href="<?php echo URLROOT; ?>/customer/login">Sign in as a customer</a>
        <a class="btn btn--outline btn--lg" href="<?php echo URLROOT; ?>/customer/supplierLogin">Sign in as a supplier</a>
      </div>
      <p class="cta__fine">New here? <a href="<?php echo URLROOT; ?>/customer/register">Create an account</a> — it takes a minute.</p>
    </div>
  </section>
</main>

<!-- ============ FOOTER ============ -->
<footer class="footer">
  <div class="footer__grid">
    <div class="footer__brand">
      <a class="brand brand--footer" href="<?php echo URLROOT; ?>/customer/index">
        <span class="brand__mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none"><rect x="2.5" y="4.5" width="19" height="15" rx="3" stroke="currentColor" stroke-width="2"/><rect x="6" y="8" width="6" height="8" rx="1.5" fill="currentColor"/></svg>
        </span>
        <span class="brand__text">
          <span class="brand__name">Budget Pilot</span>
          <span class="brand__tag">Smart finance copilot</span>
        </span>
      </a>
      <p class="footer__about">Budgeting and grocery price comparison in one place.</p>
    </div>

    <nav class="footer__col" aria-label="Product">
      <h4 class="footer__heading">Product</h4>
      <ul class="footer__links">
        <li><a href="<?php echo URLROOT; ?>/customer/dashboard">Dashboard</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/budgets">Budgets</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/grocery">Grocery catalog</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/analytics">Analytics</a></li>
      </ul>
    </nav>

    <nav class="footer__col" aria-label="Account">
      <h4 class="footer__heading">Account</h4>
      <ul class="footer__links">
        <li><a href="<?php echo URLROOT; ?>/customer/login">Customer sign in</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/supplierLogin">Supplier sign in</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/register">Create an account</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/settings">Settings</a></li>
        <li><a href="<?php echo URLROOT; ?>/customer/privacy">Privacy</a></li>
      </ul>
    </nav>

    <div class="footer__col">
      <h4 class="footer__heading">Monthly tips</h4>
      <p class="footer__about">One short email a month on cutting a grocery bill.</p>
      <div class="subscribe">
        <label class="sr-only" for="subEmail">Email</label>
        <input class="subscribe__input" id="subEmail" type="email" placeholder="name@example.com" />
        <button class="subscribe__btn" type="button" id="subBtn">Subscribe</button>
      </div>
      <p class="subscribe__msg" id="subMsg" role="status"></p>
    </div>
  </div>

  <div class="footer__bottom">
    <p>© 2026 Budget Pilot. Coursework project.</p>
    <ul class="footer__legal">
      <li><a href="<?php echo URLROOT; ?>/customer/privacy">Privacy</a></li>
      <li><a href="<?php echo URLROOT; ?>/customer/terms">Terms</a></li>
    </ul>
  </div>
</footer>

<script src="<?php echo URLROOT; ?>/public/js/customer-js/main.js"></script>
</body>
</html>
