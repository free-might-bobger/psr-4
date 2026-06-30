<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-orb orb-3"></div>
        <div class="hero-grid"></div>
      </div>
      <div class="hero-content">
        <div class="hero-text">
          <div class="hero-badge">
            <q-icon name="star" size="14px" color="amber" />
            <span>Rated #1 Local Shopping Platform</span>
            <div class="badge-dot"></div>
          </div>
          <h1 class="hero-title">
            Shop Local,<br>
            <span class="hero-title-accent">Live Better</span>
          </h1>
          <p class="hero-subtitle">
            Connect with hundreds of trusted local shops in your area. Get fresh products, great prices, and
            lightning-fast delivery — all in one place.
          </p>
          <div class="hero-actions">
            <q-btn to="/find-shops" size="lg" unelevated class="hero-btn hero-btn-primary" icon="near_me" no-caps>
              <span>Shops Near Me</span>
            </q-btn>
            <q-btn to="/find-items" size="lg" unelevated class="hero-btn hero-btn-secondary" icon="search" no-caps>
              <span>Search Items</span>
            </q-btn>
          </div>
          <div class="hero-trust">
            <div class="trust-avatars">
              <div class="trust-avatar" v-for="n in 4" :key="n">
                <q-icon name="person" size="16px" color="white" />
              </div>
            </div>
            <div class="trust-text">
              <span class="trust-count">10,000+</span> happy customers this month
            </div>
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="stat-number">500<span class="stat-plus">+</span></div>
              <div class="stat-label">Local Shops</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">50K<span class="stat-plus">+</span></div>
              <div class="stat-label">Products</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">4.9<span class="stat-plus">★</span></div>
              <div class="stat-label">App Rating</div>
            </div>
          </div>
        </div>
        <div class="hero-visual">
          <div class="hero-card-stack">
            <div class="floating-card card-delivery">
              <q-icon name="local_shipping" size="20px" color="primary" />
              <div class="floating-card-text">
                <span class="fc-label">Order Delivered</span>
                <span class="fc-sub">2 mins ago</span>
              </div>
            </div>
            <div class="hero-logo-container">
              <BiliscartLogo :size="220" />
            </div>
            <div class="floating-card card-rating">
              <q-icon name="star" size="20px" color="amber" />
              <div class="floating-card-text">
                <span class="fc-label">Top Rated</span>
                <span class="fc-sub">4.9 / 5.0</span>
              </div>
            </div>
            <div class="floating-card card-shops">
              <q-icon name="store" size="20px" color="green" />
              <div class="floating-card-text">
                <span class="fc-label">New Shop</span>
                <span class="fc-sub">Just joined!</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="hero-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="white" />
        </svg>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section">
      <div class="section-container">
        <div class="section-header">
          <div class="section-tag">Simple Process</div>
          <h2 class="section-title">From Browse to <span class="title-highlight">Doorstep</span></h2>
          <p class="section-subtitle">Get your essentials in 3 effortless steps</p>
        </div>
        <div class="steps-grid">
          <div class="step-card">
            <div class="step-icon-wrap">
              <q-icon name="location_on" size="32px" color="white" />
              <div class="step-number-badge">01</div>
            </div>
            <div class="step-connector"></div>
            <div class="step-content">
              <h3 class="step-title">Find Nearby Shops</h3>
              <p class="step-description">Enable location or search by area to instantly see all local shops stocked
                with what you need.</p>
            </div>
          </div>
          <div class="step-card">
            <div class="step-icon-wrap step-icon-2">
              <q-icon name="add_shopping_cart" size="32px" color="white" />
              <div class="step-number-badge">02</div>
            </div>
            <div class="step-connector"></div>
            <div class="step-content">
              <h3 class="step-title">Pick & Add to Cart</h3>
              <p class="step-description">Browse products, compare prices across shops, and add your favorites to a
                single cart.</p>
            </div>
          </div>
          <div class="step-card">
            <div class="step-icon-wrap step-icon-3">
              <q-icon name="rocket_launch" size="32px" color="white" />
              <div class="step-number-badge">03</div>
            </div>
            <div class="step-content">
              <h3 class="step-title">Fast Delivery</h3>
              <p class="step-description">Checkout in seconds and track your order in real-time as it heads straight to
                you.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="features-container">
        <div class="section-header">
          <div class="section-tag">Why We're Different</div>
          <h2 class="section-title">Built for <span class="title-highlight">Local Communities</span></h2>
          <p class="section-subtitle">Everything you need to shop smarter and support local</p>
        </div>
        <div class="features-grid">
          <div class="feature-card feature-card-large">
            <div class="feature-card-bg"></div>
            <div class="feature-icon-wrapper">
              <q-icon name="store" size="36px" color="white" />
            </div>
            <h3 class="feature-title">100s of Local Shops</h3>
            <p class="feature-description">Discover a curated selection of local stores — from groceries and fresh
              produce to hardware and specialty goods.</p>
            <div class="feature-tag">Most Popular</div>
          </div>

          <div class="feature-card">
            <div class="feature-icon-wrapper fi-blue">
              <q-icon name="bolt" size="32px" color="white" />
            </div>
            <h3 class="feature-title">Lightning Delivery</h3>
            <p class="feature-description">Same-day delivery from local shops means you get it faster — without the
              big-chain wait.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon-wrapper fi-green">
              <q-icon name="verified_user" size="32px" color="white" />
            </div>
            <h3 class="feature-title">Verified & Trusted</h3>
            <p class="feature-description">Every shop is manually verified for quality and reliability before listing on
              our platform.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon-wrapper fi-orange">
              <q-icon name="price_check" size="32px" color="white" />
            </div>
            <h3 class="feature-title">Price Comparison</h3>
            <p class="feature-description">See the same item across multiple shops and instantly choose the best deal
              for you.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon-wrapper fi-purple">
              <q-icon name="lock" size="32px" color="white" />
            </div>
            <h3 class="feature-title">Secure Payments</h3>
            <p class="feature-description">Your transactions are protected end-to-end with bank-grade security and
              encryption.</p>
          </div>

          <div class="feature-card">
            <div class="feature-icon-wrapper fi-teal">
              <q-icon name="headset_mic" size="32px" color="white" />
            </div>
            <h3 class="feature-title">24/7 Support</h3>
            <p class="feature-description">Our friendly support team is always a tap away — ready to resolve any issue
              in minutes.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof Section -->
    <section class="social-proof-section">
      <div class="section-container">
        <div class="proof-grid">
          <div class="proof-stat">
            <div class="proof-number">500<span>+</span></div>
            <div class="proof-label">Verified Local Shops</div>
          </div>
          <div class="proof-divider"></div>
          <div class="proof-stat">
            <div class="proof-number">50K<span>+</span></div>
            <div class="proof-label">Products Available</div>
          </div>
          <div class="proof-divider"></div>
          <div class="proof-stat">
            <div class="proof-number">10K<span>+</span></div>
            <div class="proof-label">Happy Customers</div>
          </div>
          <div class="proof-divider"></div>
          <div class="proof-stat">
            <div class="proof-number">4.9<span>★</span></div>
            <div class="proof-label">Average Rating</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-background">
        <div class="cta-orb cta-orb-1"></div>
        <div class="cta-orb cta-orb-2"></div>
        <div class="cta-orb cta-orb-3"></div>
        <div class="cta-grid"></div>
      </div>
      <div class="cta-container">
        <div class="cta-badge">
          <q-icon name="auto_awesome" size="16px" />
          <span>Free to join — no credit card needed</span>
        </div>
        <h2 class="cta-title">Your Neighborhood Store,<br><span class="cta-title-accent">One Tap Away</span></h2>
        <p class="cta-description">
          Join thousands of customers already discovering better local shopping. Sign up free and find your first shop
          in under 30 seconds.
        </p>
        <div class="cta-actions">
          <q-btn to="/find-shops" size="lg" unelevated class="cta-btn cta-btn-primary" icon="near_me" no-caps>
            <span class="gt-xs">Find Shops Now</span>
            <span class="lt-sm">Find Shops</span>
          </q-btn>
          <q-btn to="/register" size="lg" unelevated class="cta-btn cta-btn-secondary" icon="person_add" no-caps>
            <span class="gt-xs">Create Free Account</span>
            <span class="lt-sm">Sign Up Free</span>
          </q-btn>
        </div>
        <div class="cta-footnote">
          <q-icon name="lock" size="14px" />
          <span>Secure · Private · Always Free to Browse</span>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import BiliscartLogo from 'src/components/BiliscartLogo.vue';
import { useQuasar } from 'quasar';

const $q = useQuasar();
const success = process.env.SERVER ? null : new URLSearchParams(window.location.search).get('success')?.trim();

if (process.env.CLIENT && success === 'true') {
  $q.notify({
    type: 'positive',
    message: 'Account activated successfully',
    position: 'top',
    timeout: 0,
    closeBtn: 'OK'
  });
}

</script>

<style scoped lang="scss">
// ── Base ──────────────────────────────────────────────────────────────────────
.home-page {
  min-height: calc(100vh - 64px);
  overflow-x: hidden;
  background: #ffffff;
}

// ── Shared ───────────────────────────────────────────────────────────────────
.section-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 28px;
}

.section-header {
  text-align: center;
  margin-bottom: 64px;
}

.section-tag {
  display: inline-block;
  background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
  color: #6d28d9;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  padding: 6px 16px;
  border-radius: 20px;
  margin-bottom: 16px;
}

.section-title {
  font-size: 44px;
  font-weight: 800;
  color: #111827;
  margin: 0 0 16px;
  line-height: 1.15;
  letter-spacing: -0.5px;
}

.title-highlight {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  font-size: 18px;
  color: #6b7280;
  margin: 0;
  line-height: 1.6;
}

// ── Hero ─────────────────────────────────────────────────────────────────────
.hero-section {
  position: relative;
  background: linear-gradient(145deg, #1e1b4b 0%, #312e81 40%, #4c1d95 70%, #6d28d9 100%);
  padding: 110px 28px 0;
  color: white;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  inset: 0;
  overflow: hidden;
  z-index: 0;
}

.hero-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  animation: floatOrb 10s ease-in-out infinite;

  &.orb-1 {
    width: 500px;
    height: 500px;
    background: rgba(139, 92, 246, 0.35);
    top: -150px;
    right: -100px;
    animation-delay: 0s;
  }

  &.orb-2 {
    width: 350px;
    height: 350px;
    background: rgba(99, 102, 241, 0.3);
    bottom: 0;
    left: -80px;
    animation-delay: 3s;
  }

  &.orb-3 {
    width: 250px;
    height: 250px;
    background: rgba(167, 139, 250, 0.2);
    top: 40%;
    left: 40%;
    animation-delay: 6s;
  }
}

.hero-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
  background-size: 60px 60px;
}

@keyframes floatOrb {

  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }

  33% {
    transform: translate(20px, -30px) scale(1.05);
  }

  66% {
    transform: translate(-15px, 20px) scale(0.95);
  }
}

.hero-content {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 80px;
  z-index: 1;
  flex-wrap: wrap;
}

.hero-text {
  flex: 1;
  min-width: 340px;
  animation: heroFadeIn 0.8s ease-out both;
}

@keyframes heroFadeIn {
  from {
    opacity: 0;
    transform: translateY(24px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  padding: 7px 16px;
  border-radius: 24px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 28px;
  color: #e9d5ff;
  position: relative;
}

.badge-dot {
  width: 6px;
  height: 6px;
  background: #4ade80;
  border-radius: 50%;
  box-shadow: 0 0 6px #4ade80;
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {

  0%,
  100% {
    opacity: 1;
    transform: scale(1);
  }

  50% {
    opacity: 0.5;
    transform: scale(1.4);
  }
}

.hero-title {
  font-size: 64px;
  font-weight: 900;
  margin: 0 0 24px;
  line-height: 1.05;
  letter-spacing: -1.5px;
  color: white;
}

.hero-title-accent {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #fcd34d 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 19px;
  line-height: 1.75;
  margin: 0 0 40px;
  color: rgba(255, 255, 255, 0.8);
  max-width: 520px;
}

.hero-actions {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
  margin-bottom: 36px;
}

.hero-btn {
  height: 54px;
  font-weight: 700;
  font-size: 15px;
  border-radius: 14px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 0 28px;

  &.hero-btn-primary {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1a1a1a;
    box-shadow: 0 8px 24px rgba(251, 191, 36, 0.35);

    &:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 32px rgba(251, 191, 36, 0.5);
    }
  }

  &.hero-btn-secondary {
    background: rgba(255, 255, 255, 0.12);
    color: white;
    border: 1.5px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);

    &:hover {
      background: rgba(255, 255, 255, 0.22);
      transform: translateY(-3px);
      border-color: rgba(255, 255, 255, 0.5);
    }
  }
}

.hero-trust {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 40px;
}

.trust-avatars {
  display: flex;
}

.trust-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #7c3aed, #4f46e5);
  border: 2px solid rgba(255, 255, 255, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: -8px;

  &:first-child {
    margin-left: 0;
  }
}

.trust-text {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.75);
}

.trust-count {
  font-weight: 700;
  color: #fbbf24;
}

.hero-stats {
  display: flex;
  align-items: center;
  gap: 0;
  padding: 20px 28px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  width: fit-content;
  animation: heroFadeIn 0.8s ease-out 0.3s both;
}

.stat-item {
  text-align: center;
  padding: 0 28px;
}

.stat-number {
  font-size: 28px;
  font-weight: 800;
  color: white;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-plus {
  font-size: 20px;
  color: #fbbf24;
}

.stat-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.65);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.15);
  flex-shrink: 0;
}

// Hero Visual / Right Side
.hero-visual {
  flex: 0 0 420px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding-bottom: 40px;
  animation: heroFadeIn 0.8s ease-out 0.2s both;
}

.hero-card-stack {
  position: relative;
  width: 320px;
  height: 360px;
}

.hero-logo-container {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(20px);
  border-radius: 28px;
  padding: 40px;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3);
}

.floating-card {
  position: absolute;
  display: flex;
  align-items: center;
  gap: 10px;
  background: white;
  border-radius: 14px;
  padding: 12px 16px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
  white-space: nowrap;
  animation: floatCard 5s ease-in-out infinite;

  &.card-delivery {
    top: 10px;
    left: -20px;
    animation-delay: 0s;
  }

  &.card-rating {
    bottom: 20px;
    right: -20px;
    animation-delay: 1.5s;
  }

  &.card-shops {
    bottom: 80px;
    left: -30px;
    animation-delay: 3s;
  }
}

@keyframes floatCard {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}

.floating-card-text {
  display: flex;
  flex-direction: column;
}

.fc-label {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  line-height: 1;
}

.fc-sub {
  font-size: 11px;
  color: #6b7280;
  margin-top: 2px;
}

// Wave
.hero-wave {
  margin-top: 60px;
  line-height: 0;

  svg {
    display: block;
    width: 100%;
    height: 80px;
  }
}

// ── How It Works ──────────────────────────────────────────────────────────────
.how-it-works-section {
  padding: 100px 0 110px;
  background: white;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
  position: relative;

  &::before {
    content: '';
    position: absolute;
    top: 52px;
    left: calc(16.67% + 16px);
    right: calc(16.67% + 16px);
    height: 2px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    opacity: 0.25;
    z-index: 0;
  }
}

.step-card {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 40px 28px;
  background: white;
  border-radius: 20px;
  border: 1px solid #f3f4f6;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);

  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 48px rgba(102, 126, 234, 0.12);
    border-color: #ddd6fe;

    .step-icon-wrap {
      transform: scale(1.08);
    }
  }
}

.step-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 28px;
  position: relative;
  flex-shrink: 0;
  transition: transform 0.3s ease;
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);

  &.step-icon-2 {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 8px 24px rgba(245, 87, 108, 0.35);
  }

  &.step-icon-3 {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 8px 24px rgba(79, 172, 254, 0.35);
  }
}

.step-number-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  background: white;
  border-radius: 50%;
  font-size: 10px;
  font-weight: 800;
  color: #374151;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
  border: 1.5px solid #e5e7eb;
}

.step-content {
  flex: 1;
}

.step-title {
  font-size: 20px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 12px;
}

.step-description {
  font-size: 15px;
  line-height: 1.65;
  color: #6b7280;
  margin: 0;
}

// ── Features ──────────────────────────────────────────────────────────────────
.features-section {
  padding: 100px 0 110px;
  background: linear-gradient(180deg, #fafaf9 0%, #f3f4f6 100%);
}

.features-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 28px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: auto auto;
  gap: 24px;
}

.feature-card {
  position: relative;
  background: white;
  border-radius: 20px;
  padding: 32px;
  border: 1px solid #e9ecef;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;

  &:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 48px rgba(0, 0, 0, 0.09);
    border-color: #ddd6fe;

    .feature-icon-wrapper {
      transform: scale(1.1) rotate(-5deg);
    }
  }

  &.feature-card-large {
    grid-column: span 1;
    background: linear-gradient(145deg, #1e1b4b 0%, #312e81 60%, #4c1d95 100%);
    border: none;
    color: white;

    &:hover {
      box-shadow: 0 20px 48px rgba(79, 70, 229, 0.3);
      border: none;
    }

    .feature-title {
      color: white;
    }

    .feature-description {
      color: rgba(255, 255, 255, 0.75);
    }
  }
}

.feature-card-bg {
  position: absolute;
  top: -40px;
  right: -40px;
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.06);
}

.feature-icon-wrapper {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  transition: transform 0.3s ease;
  box-shadow: 0 6px 16px rgba(102, 126, 234, 0.3);

  &.fi-blue {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    box-shadow: 0 6px 16px rgba(79, 172, 254, 0.3);
  }

  &.fi-green {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    box-shadow: 0 6px 16px rgba(67, 233, 123, 0.3);
  }

  &.fi-orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 6px 16px rgba(245, 87, 108, 0.3);
  }

  &.fi-purple {
    background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
    box-shadow: 0 6px 16px rgba(161, 140, 209, 0.3);
  }

  &.fi-teal {
    background: linear-gradient(135deg, #0fd850 0%, #f9f047 100%);
    box-shadow: 0 6px 16px rgba(15, 216, 80, 0.3);
  }
}

.feature-title {
  font-size: 19px;
  font-weight: 700;
  color: #111827;
  margin: 0 0 10px;
}

.feature-description {
  font-size: 14px;
  line-height: 1.65;
  color: #6b7280;
  margin: 0;
}

.feature-tag {
  display: inline-flex;
  align-items: center;
  margin-top: 20px;
  padding: 5px 14px;
  background: rgba(251, 191, 36, 0.2);
  color: #fbbf24;
  font-size: 12px;
  font-weight: 700;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: 1px solid rgba(251, 191, 36, 0.3);
}

// ── Social Proof ──────────────────────────────────────────────────────────────
.social-proof-section {
  padding: 72px 0;
  background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
}

.proof-grid {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
  flex-wrap: wrap;
}

.proof-stat {
  text-align: center;
  padding: 20px 48px;
}

.proof-number {
  font-size: 48px;
  font-weight: 900;
  color: white;
  line-height: 1;
  margin-bottom: 8px;
  letter-spacing: -1px;

  span {
    color: #fbbf24;
    font-size: 36px;
  }
}

.proof-label {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.proof-divider {
  width: 1px;
  height: 60px;
  background: rgba(255, 255, 255, 0.15);
  flex-shrink: 0;
}

// ── CTA ───────────────────────────────────────────────────────────────────────
.cta-section {
  position: relative;
  padding: 120px 28px;
  background: linear-gradient(145deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
  overflow: hidden;
  text-align: center;
  color: white;
}

.cta-background {
  position: absolute;
  inset: 0;
  overflow: hidden;
  z-index: 0;
}

.cta-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  animation: floatOrb 12s ease-in-out infinite;

  &.cta-orb-1 {
    width: 500px;
    height: 500px;
    background: rgba(109, 40, 217, 0.35);
    top: -200px;
    left: -100px;
  }

  &.cta-orb-2 {
    width: 400px;
    height: 400px;
    background: rgba(99, 102, 241, 0.25);
    bottom: -150px;
    right: -100px;
    animation-delay: 4s;
  }

  &.cta-orb-3 {
    width: 250px;
    height: 250px;
    background: rgba(251, 191, 36, 0.12);
    top: 40%;
    left: 50%;
    animation-delay: 8s;
  }
}

.cta-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
  background-size: 60px 60px;
}

.cta-container {
  position: relative;
  max-width: 760px;
  margin: 0 auto;
  z-index: 1;
}

.cta-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(251, 191, 36, 0.15);
  border: 1px solid rgba(251, 191, 36, 0.3);
  color: #fbbf24;
  font-size: 13px;
  font-weight: 600;
  padding: 7px 18px;
  border-radius: 24px;
  margin-bottom: 28px;
}

.cta-title {
  font-size: 52px;
  font-weight: 900;
  color: white;
  margin: 0 0 20px;
  line-height: 1.1;
  letter-spacing: -1px;
}

.cta-title-accent {
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.cta-description {
  font-size: 18px;
  line-height: 1.7;
  color: rgba(255, 255, 255, 0.72);
  margin: 0 0 44px;
  max-width: 560px;
  margin-left: auto;
  margin-right: auto;
}

.cta-actions {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 28px;
}

.cta-btn {
  height: 56px;
  font-weight: 700;
  font-size: 16px;
  border-radius: 14px;
  padding: 0 32px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &.cta-btn-primary {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1a1a1a;
    box-shadow: 0 8px 24px rgba(251, 191, 36, 0.35);

    &:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 36px rgba(251, 191, 36, 0.5);
    }
  }

  &.cta-btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1.5px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);

    &:hover {
      background: rgba(255, 255, 255, 0.18);
      transform: translateY(-3px);
      border-color: rgba(255, 255, 255, 0.45);
    }
  }
}

.cta-footnote {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: rgba(255, 255, 255, 0.4);
  font-size: 13px;
  font-weight: 500;
}

// ── Responsive ────────────────────────────────────────────────────────────────
@media (max-width: 1024px) {
  .hero-title {
    font-size: 52px;
  }

  .hero-visual {
    flex: 0 0 340px;
  }

  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .features-grid .feature-card-large {
    grid-column: span 2;
  }

  .steps-grid::before {
    display: none;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 80px 20px 0;
  }

  .hero-content {
    flex-direction: column-reverse;
    gap: 40px;
    text-align: center;
  }

  .hero-title {
    font-size: 42px;
    letter-spacing: -0.5px;
  }

  .hero-subtitle {
    font-size: 17px;
    margin: 0 auto 36px;
  }

  .hero-actions {
    justify-content: center;
    flex-direction: column;
    align-items: center;
  }

  .hero-btn {
    width: 100%;
    max-width: 320px;
  }

  .hero-trust {
    justify-content: center;
  }

  .hero-stats {
    width: 100%;
    justify-content: center;
    gap: 0;
  }

  .hero-visual {
    flex: 0 0 auto;
  }

  .hero-card-stack {
    width: 260px;
    height: 280px;
  }

  .section-title {
    font-size: 32px;
  }

  .section-subtitle {
    font-size: 16px;
  }

  .section-header {
    margin-bottom: 40px;
  }

  .steps-grid {
    grid-template-columns: 1fr;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .features-grid .feature-card-large {
    grid-column: span 1;
  }

  .proof-grid {
    gap: 0;
  }

  .proof-stat {
    padding: 16px 24px;
  }

  .proof-number {
    font-size: 36px;
  }

  .proof-divider {
    height: 40px;
  }

  .cta-title {
    font-size: 36px;
  }

  .cta-description {
    font-size: 16px;
  }

  .cta-actions {
    flex-direction: column;
  }

  .cta-btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 34px;
  }

  .hero-subtitle {
    font-size: 15px;
  }

  .section-title {
    font-size: 26px;
  }

  .cta-title {
    font-size: 28px;
  }

  .stat-item {
    padding: 0 16px;
  }

  .stat-number {
    font-size: 22px;
  }

  .proof-stat {
    padding: 12px 16px;
  }

  .proof-number {
    font-size: 30px;
  }
}
</style>
