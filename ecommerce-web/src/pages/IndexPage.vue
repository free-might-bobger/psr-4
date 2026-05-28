<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-shape shape-1"></div>
        <div class="hero-shape shape-2"></div>
        <div class="hero-shape shape-3"></div>
      </div>
      <div class="hero-content">
        <div class="hero-text">
          <div class="hero-badge">
            <q-icon name="star" size="16px" />
            <span>Trusted by 10,000+ Customers</span>
          </div>
          <h1 class="hero-title">
            Discover Local Shops<br>
            <span class="brand-name">Near You</span>
          </h1>
          <p class="hero-subtitle">
            Your trusted marketplace for daily essentials. Browse products from multiple shops, compare prices, and get fast delivery to your doorstep.
          </p>
          <div class="hero-actions">
            <q-btn to="/find-shops" color="white" text-color="primary" size="lg" unelevated class="hero-btn primary-btn" icon="store"
              label="Find Shops Near Me" />
            <q-btn to="/find-items" style="color: #fee140; background: transparent; border: 2px solid #fee140;" size="lg" unelevated class="hero-btn outline-btn" icon="shopping_bag"
              label="Find Items Near Me" />
          </div>
          <div class="hero-stats">
            <div class="stat-item">
              <div class="stat-number">500+</div>
              <div class="stat-label">Local Shops</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">50K+</div>
              <div class="stat-label">Products</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-number">10K+</div>
              <div class="stat-label">Happy Customers</div>
            </div>
          </div>
        </div>
        <div class="hero-image">
          <BiliscartLogo :size="280" />
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section">
      <div class="section-container">
        <div class="section-header">
          <h2 class="section-title">How It Works</h2>
          <p class="section-subtitle">Get your essentials delivered in 3 simple steps</p>
        </div>
        <div class="steps-grid">
          <div class="step-card">
            <div class="step-number">01</div>
            <div class="step-content">
              <h3 class="step-title">Find Shops</h3>
              <p class="step-description">Browse shops near your location and discover a wide range of products</p>
            </div>
          </div>
          <div class="step-card">
            <div class="step-number">02</div>
            <div class="step-content">
              <h3 class="step-title">Add to Cart</h3>
              <p class="step-description">Select your favorite items and add them to your cart</p>
            </div>
          </div>
          <div class="step-card">
            <div class="step-number">03</div>
            <div class="step-content">
              <h3 class="step-title">Get Delivered</h3>
              <p class="step-description">Place your order and enjoy fast delivery to your doorstep</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="features-container">
        <div class="section-header">
          <h2 class="section-title">Why Choose Us?</h2>
          <p class="section-subtitle">Experience the best local shopping platform</p>
        </div>
        <div class="features-grid">
          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon-wrapper">
                <q-icon name="store" size="40px" color="primary" />
              </div>
                <h3 class="feature-title">Multiple Shops</h3>
            
              <p class="feature-description">
                Browse products from various local shops all in one place. Compare prices and find the best deals.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon-wrapper">
                <q-icon name="local_shipping" size="40px" color="primary" />
              </div>
              <h3 class="feature-title">Fast Delivery</h3>
              <p class="feature-description">
                Get your orders delivered quickly and safely. Track your delivery in real-time.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon-wrapper">
                <q-icon name="security" size="40px" color="primary" />
              </div>
              <h3 class="feature-title">Secure Shopping</h3>
              <p class="feature-description">
                Shop with confidence. Your payment information is protected with industry-standard security.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon-wrapper">
                <q-icon name="support_agent" size="40px" color="primary" />
              </div>
              <h3 class="feature-title">24/7 Support</h3>
              <p class="feature-description">
                Our customer support team is always ready to help you with any questions.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon-wrapper">
                <q-icon name="compare_arrows" size="40px" color="primary" />
              </div>
              <h3 class="feature-title">Price Comparison</h3>
              <p class="feature-description">
                Compare prices across different shops to get the best value for your money.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon-wrapper">
                <q-icon name="verified" size="40px" color="primary" />
              </div>
              <h3 class="feature-title">Verified Shops</h3>
              <p class="feature-description">
                All shops are verified to ensure quality products and reliable service.
              </p>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <div class="cta-background">
        <div class="cta-shape shape-1"></div>
        <div class="cta-shape shape-2"></div>
      </div>
      <div class="cta-container">
        <div class="cta-content">
          <h2 class="cta-title">Ready to Start Shopping?</h2>
          <p class="cta-description">
            Join thousands of happy customers and discover the convenience of local shopping.
          </p>
          <div class="cta-actions">
            <q-btn to="/find-shops" color="white" text-color="primary" size="lg" unelevated class="cta-button" icon="store"
              label="Find Shops Now" />
            <q-btn to="/register" style="color: #fee140; background: transparent; border: 2px solid #fee140;" size="lg" unelevated class="cta-button outline" icon="person_add"
              label="Sign Up Free" />
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import BiliscartLogo from 'src/components/BiliscartLogo.vue';
import { useQuasar } from 'quasar';

const $q = useQuasar();
const urlParams = new URLSearchParams(window.location.search);
const success = urlParams.get('success')?.trim();

if (success === 'true') {
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
.home-page {
  min-height: calc(100vh - 64px);
  overflow-x: hidden;
}

/* Hero Section */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 100px 24px 120px;
  color: white;
  overflow: hidden;
}

.hero-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.hero-shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 6s ease-in-out infinite;

  &.shape-1 {
    width: 400px;
    height: 400px;
    top: -100px;
    right: -100px;
    animation-delay: 0s;
  }

  &.shape-2 {
    width: 300px;
    height: 300px;
    bottom: -50px;
    left: -50px;
    animation-delay: 2s;
  }

  &.shape-3 {
    width: 200px;
    height: 200px;
    top: 50%;
    right: 20%;
    animation-delay: 4s;
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(5deg);
  }
}

.hero-content {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 80px;
  flex-wrap: wrap;
  z-index: 1;
}

.hero-text {
  flex: 1;
  min-width: 350px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(254, 225, 64, 0.25);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 24px;
  backdrop-filter: blur(10px);
  animation: slideDown 0.6s ease-out;
  color: #1a1a1a;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-title {
  font-size: 56px;
  font-weight: 800;
  margin: 0 0 24px 0;
  line-height: 1.1;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  animation: slideUp 0.6s ease-out 0.2s both;

  .brand-name {
    color: white;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-subtitle {
  font-size: 20px;
  line-height: 1.7;
  margin: 0 0 40px 0;
  opacity: 0.95;
  max-width: 550px;
  animation: slideUp 0.6s ease-out 0.4s both;
}

.hero-actions {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 48px;
  animation: slideUp 0.6s ease-out 0.6s both;
}

.hero-btn {
  min-width: 180px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  border-radius: 12px;
  transition: all 0.3s ease;
  white-space: nowrap;

  &.primary-btn {
    background: #fee140;
    color: #1a1a1a;

    &:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(254, 225, 64, 0.4);
      background: #ffd700;
    }
  }

  &.outline-btn {
    background: transparent;
    border: 2px solid #fee140;
    color: #fee140;

    &:hover {
      background: rgba(254, 225, 64, 0.15);
      transform: translateY(-3px);
    }
  }
}

.hero-stats {
  display: flex;
  align-items: center;
  gap: 32px;
  padding-top: 32px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  animation: slideUp 0.6s ease-out 0.8s both;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 13px;
  opacity: 0.9;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.3);
}

.hero-image {
  flex: 0 0 350px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 24px;
  padding: 50px;
  backdrop-filter: blur(10px);
  animation: scaleIn 0.8s ease-out 0.4s both;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Section Styles */
.section-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.section-title {
  font-size: 42px;
  font-weight: 800;
  margin: 0 0 16px 0;
  color: #1a1a1a;
}

.section-subtitle {
  font-size: 18px;
  color: #666;
  margin: 0;
}

/* How It Works Section */
.how-it-works-section {
  padding: 100px 0;
  background: white;
}

.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.step-card {
  display: flex;
  gap: 20px;
  align-items: flex-start;
  padding: 32px;
  background: #f8f9fa;
  border-radius: 16px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
  }
}

.step-number {
  font-size: 48px;
  font-weight: 800;
  color: var(--q-primary);
  opacity: 0.3;
  line-height: 1;
  min-width: 60px;
}

.step-content {
  flex: 1;
}

.step-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #1a1a1a;
}

.step-description {
  font-size: 15px;
  line-height: 1.6;
  color: #666;
  margin: 0;
}

/* Features Section */
.features-section {
  padding: 100px 0;
  background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.features-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 24px;
}

.feature-card {
  border-radius: 16px;
  transition: all 0.3s ease;
  height: 100%;
  background: white;
  border: 1px solid #e0e0e0;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    border-color: var(--q-primary);
  }
}

.feature-icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 70px;
  height: 70px;
  border-radius: 16px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  margin-bottom: 20px;
  color: #1976d2;
  transition: all 0.3s ease;

  .feature-card:hover & {
    transform: scale(1.1) rotate(5deg);
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1565c0;
  }
}

.feature-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #1a1a1a;
}

.feature-description {
  font-size: 15px;
  line-height: 1.6;
  color: #666;
  margin: 0;
}

/* CTA Section */
.cta-section {
  position: relative;
  padding: 120px 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  overflow: hidden;
}

.cta-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  z-index: 0;
}

.cta-shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 8s ease-in-out infinite;

  &.shape-1 {
    width: 500px;
    height: 500px;
    top: -200px;
    left: -200px;
    animation-delay: 0s;
  }

  &.shape-2 {
    width: 400px;
    height: 400px;
    bottom: -150px;
    right: -150px;
    animation-delay: 3s;
  }
}

.cta-container {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
  padding: 0 24px;
  z-index: 1;
}

.cta-content {
  text-align: center;
  color: white;
}

.cta-title {
  font-size: 42px;
  font-weight: 800;
  margin: 0 0 20px 0;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.cta-description {
  font-size: 20px;
  line-height: 1.6;
  margin: 0 0 40px 0;
  opacity: 0.95;
}

.cta-actions {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}

.cta-button {
  min-width: 180px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  border-radius: 12px;
  transition: all 0.3s ease;

  &:not(.outline) {
    background: #fee140;
    color: #1a1a1a;

    &:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(254, 225, 64, 0.4);
      background: #ffd700;
    }
  }

  &.outline {
    background: transparent;
    border: 2px solid #fee140;
    color: #fee140;

    &:hover {
      background: rgba(254, 225, 64, 0.15);
      transform: translateY(-3px);
    }
  }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .hero-content {
    gap: 60px;
  }

  .hero-image {
    flex: 0 0 300px;
  }

  .steps-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .steps-grid .step-card:last-child {
    grid-column: span 2;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 80px 20px 100px;
  }

  .hero-content {
    flex-direction: column;
    gap: 60px;
    text-align: center;
  }

  .hero-title {
    font-size: 42px;
  }

  .hero-subtitle {
    font-size: 18px;
  }

  .hero-image {
    flex: 0 0 auto;
    width: 100%;
    max-width: 350px;
  }

  .hero-actions {
    width: 100%;
    justify-content: center;
    flex-direction: column;
  }

  .hero-btn {
    width: 100%;
    min-width: 0;
  }

  .hero-stats {
    justify-content: center;
    gap: 24px;
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

  .how-it-works-section {
    padding: 80px 0;
  }

  .steps-grid {
    grid-template-columns: 1fr;
  }

  .steps-grid .step-card:last-child {
    grid-column: span 1;
  }

  .features-section {
    padding: 80px 0;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .cta-section {
    padding: 80px 0;
  }

  .cta-title {
    font-size: 32px;
  }

  .cta-description {
    font-size: 18px;
  }

  .cta-actions {
    flex-direction: column;
  }

  .cta-button {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .hero-title {
    font-size: 32px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .hero-badge {
    font-size: 13px;
    padding: 6px 12px;
  }

  .hero-stats {
    gap: 16px;
  }

  .stat-number {
    font-size: 24px;
  }

  .stat-label {
    font-size: 12px;
  }

  .section-title {
    font-size: 28px;
  }

  .step-card {
    padding: 24px;
  }

  .step-number {
    font-size: 36px;
    min-width: 50px;
  }

  .step-title {
    font-size: 18px;
  }

  .cta-title {
    font-size: 28px;
  }
}
</style>
