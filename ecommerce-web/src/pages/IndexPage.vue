<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <div class="hero-text">
          <h1 class="hero-title">
            Welcome to <span class="brand-name">Zetenpo</span>
          </h1>
          <p class="hero-subtitle">
            Your trusted marketplace for daily essentials. Discover multiple stores, compare prices, and shop with
            confidence.
          </p>
          <div class="hero-actions">
            <q-btn to="/find-store" color="primary" size="lg" unelevated class="hero-btn" icon="explore"
              label="Find Stores Near Me" />
            <q-btn to="/cart" color="secondary" size="lg" outline class="hero-btn" icon="shopping_cart"
              label="View Cart" />
          </div>
        </div>
        <div class="hero-image">
          <ZetenpoLogo :size="200" />
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
      <div class="features-container">
        <h2 class="section-title">Why Choose Zetenpo?</h2>
        <div class="features-grid">
          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon">
                <q-icon name="store" size="48px" color="primary" />
              </div>
              <h3 class="feature-title">Multiple Stores</h3>
              <p class="feature-description">
                Browse products from various stores all in one place. Compare prices and find the best deals.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon">
                <q-icon name="local_shipping" size="48px" color="primary" />
              </div>
              <h3 class="feature-title">Fast Delivery</h3>
              <p class="feature-description">
                Get your orders delivered quickly and safely to your doorstep. Track your delivery in real-time.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon">
                <q-icon name="security" size="48px" color="primary" />
              </div>
              <h3 class="feature-title">Secure Shopping</h3>
              <p class="feature-description">
                Shop with confidence. Your payment information is protected with industry-standard security.
              </p>
            </q-card-section>
          </q-card>

          <q-card flat bordered class="feature-card">
            <q-card-section>
              <div class="feature-icon">
                <q-icon name="support_agent" size="48px" color="primary" />
              </div>
              <h3 class="feature-title">24/7 Support</h3>
              <p class="feature-description">
                Our customer support team is always ready to help you with any questions or concerns.
              </p>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
      <q-card flat bordered class="cta-card">
        <q-card-section class="cta-content">
          <div class="cta-text">
            <h2 class="cta-title">Ready to Start Shopping?</h2>
            <p class="cta-description">
              Find stores near you and discover amazing products at great prices.
            </p>
          </div>
          <q-btn to="/find-store" color="primary" size="lg" unelevated class="cta-button" icon="explore"
            label="Find Stores Now" />
        </q-card-section>
      </q-card>
    </section>
  </div>
</template>

<script setup lang="ts">
import ZetenpoLogo from 'src/components/ZetenpoLogo.vue';
</script>

<style scoped lang="scss">
.home-page {
  min-height: calc(100vh - 64px);
}

.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 80px 24px;
  color: white;
}

.hero-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 60px;
  flex-wrap: wrap;
}

.hero-text {
  flex: 1;
  min-width: 300px;
}

.hero-title {
  font-size: 48px;
  font-weight: 700;
  margin: 0 0 24px 0;
  line-height: 1.2;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

  .brand-name {
    color: #fee140;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }
}

.hero-subtitle {
  font-size: 20px;
  line-height: 1.6;
  margin: 0 0 32px 0;
  opacity: 0.95;
  max-width: 600px;
}

.hero-actions {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.hero-btn {
  min-width: 200px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  white-space: nowrap;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  }
}

.hero-image {
  flex: 0 0 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 40px;
  backdrop-filter: blur(10px);
}

.features-section {
  padding: 80px 24px;
  background: #f8f9fa;
}

.features-container {
  max-width: 1200px;
  margin: 0 auto;
}

.section-title {
  font-size: 36px;
  font-weight: 700;
  text-align: center;
  margin: 0 0 48px 0;
  color: #1a1a1a;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.feature-card {
  border-radius: 16px;
  transition: all 0.3s ease;
  height: 100%;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);

  &:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }
}

.feature-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 80px;
  height: 80px;
  border-radius: 16px;
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  margin: 0 auto 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.feature-title {
  font-size: 22px;
  font-weight: 600;
  text-align: center;
  margin: 0 0 12px 0;
  color: #1a1a1a;
}

.feature-description {
  font-size: 15px;
  line-height: 1.6;
  text-align: center;
  color: #666;
  margin: 0;
}

.cta-section {
  padding: 80px 24px;
  background: white;
}

.cta-card {
  max-width: 1000px;
  margin: 0 auto;
  border-radius: 20px;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.cta-content {
  padding: 48px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
  flex-wrap: wrap;
}

.cta-text {
  flex: 1;
  min-width: 300px;
  text-align: left;
}

.cta-title {
  font-size: 32px;
  font-weight: 700;
  margin: 0 0 12px 0;
  color: #1a1a1a;
}

.cta-description {
  font-size: 18px;
  color: #666;
  margin: 0;
  line-height: 1.6;
}

.cta-button {
  min-width: 200px;
  height: 52px;
  font-weight: 600;
  font-size: 16px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding: 60px 20px;
  }

  .hero-content {
    flex-direction: column;
    gap: 40px;
    text-align: center;
  }

  .hero-title {
    font-size: 36px;
  }

  .hero-subtitle {
    font-size: 18px;
  }

  .hero-image {
    flex: 0 0 auto;
    width: 100%;
    max-width: 300px;
  }

  .hero-actions {
    width: 100%;
    justify-content: center;
    flex-direction: column;
  }

  .hero-btn {
    width: 100%;
    min-width: 0;
    font-size: 15px;
  }

  .features-section {
    padding: 60px 20px;
  }

  .section-title {
    font-size: 28px;
    margin-bottom: 32px;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .cta-section {
    padding: 60px 20px;
  }

  .cta-content {
    flex-direction: column;
    text-align: center;
    padding: 32px;
    align-items: center;
  }

  .cta-text {
    min-width: 0;
    width: 100%;
    text-align: center;
  }

  .cta-title {
    font-size: 24px;
    text-align: center;
  }

  .cta-description {
    font-size: 16px;
    text-align: center;
  }

  .cta-button {
    width: 100%;
    text-align: center;
    justify-content: center;
  }
}

@media (max-width: 600px) {
  .hero-title {
    font-size: 28px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .hero-actions {
    flex-direction: column;
  }

  .hero-btn {
    width: 100%;
    min-width: 0;
    font-size: 14px;
    padding: 0 16px;
    white-space: normal;
    height: auto;
    min-height: 48px;
    line-height: 1.4;
  }
}

@media (max-width: 400px) {
  .hero-btn {
    font-size: 13px;
    padding: 0 12px;
  }

  .cta-section {
    padding: 40px 16px;
  }

  .cta-content {
    padding: 24px 16px;
  }

  .cta-title {
    font-size: 20px;
  }

  .cta-description {
    font-size: 14px;
  }

  .cta-button {
    font-size: 14px;
    padding: 0 16px;
    white-space: normal;
    height: auto;
    min-height: 48px;
    line-height: 1.4;
  }
}

@media (max-width: 370px) {
  .cta-section {
    padding: 32px 12px;
  }

  .cta-content {
    padding: 20px 12px;
  }

  .cta-title {
    font-size: 18px;
    margin-bottom: 8px;
  }

  .cta-description {
    font-size: 13px;
    line-height: 1.5;
  }

  .cta-button {
    font-size: 13px;
    padding: 0 12px;
    min-height: 44px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
}
</style>
