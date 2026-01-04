<template>
    <q-footer class="main-footer bg-white">
        <div class="footer-content">
            <div class="footer-section">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <div class="footer-logo-section">
                        <div class="footer-logo">
                            <ZetenpoLogo :size="40" />
                        </div>
                        <div class="footer-brand-text">
                            <div class="footer-brand-name">Zetenpo</div>
                            <div class="footer-brand-tagline">Your Trusted Online Shop</div>
                        </div>
                    </div>
                    <p class="footer-description">
                        We provide quality products with excellent customer service.
                        Shop with confidence and enjoy fast, reliable delivery.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="footer-links">
                    <div class="footer-link-group">
                        <div class="footer-link-title">Quick Links</div>
                        <router-link to="/" class="footer-link">Home</router-link>
                        <router-link to="/cart" class="footer-link">Shopping Cart</router-link>
                        <router-link to="/login" class="footer-link" v-if="!profile.token">Login</router-link>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="footer-contact">
                    <div class="footer-link-title">Contact Us</div>
                    <div class="footer-contact-item">
                        <q-icon name="phone" size="sm" class="q-mr-sm" />
                        <span>+63 XXX XXX XXXX</span>
                    </div>
                    <div class="footer-contact-item">
                        <q-icon name="email" size="sm" class="q-mr-sm" />
                        <span>support@zetenpo.com</span>
                    </div>
                    <div class="footer-contact-item">
                        <q-icon name="location_on" size="sm" class="q-mr-sm" />
                        <span>Philippines</span>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <q-separator class="footer-separator" />
            <div class="footer-bottom">
                <div class="footer-copyright">
                    <q-icon name="copyright" size="sm" class="q-mr-xs" />
                    <span>2024 Zetenpo. All rights reserved.</span>
                </div>
                <div class="footer-social">
                    <q-btn flat round icon="facebook" size="sm" class="social-btn" />
                    <q-btn flat round icon="twitter" size="sm" class="social-btn" />
                    <q-btn flat round icon="instagram" size="sm" class="social-btn" />
                </div>
            </div>
        </div>
    </q-footer>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useUserStore } from 'src/stores/user';
import ZetenpoLogo from 'src/components/ZetenpoLogo.vue';

const { profile } = storeToRefs(useUserStore());
</script>

<style scoped lang="scss">
.main-footer {
    border-top: 1px solid #e0e0e0;
    box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 16px 24px;
}

.footer-section {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 40px;
    margin-bottom: 32px;
}

.footer-brand {
    .footer-logo-section {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        border: 2px solid #f5f5f5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 2px;
    }

    .footer-brand-text {
        display: flex;
        flex-direction: column;
    }

    .footer-brand-name {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1.2;
    }

    .footer-brand-tagline {
        font-size: 12px;
        color: #666;
        line-height: 1;
        margin-top: 2px;
    }

    .footer-description {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }
}

.footer-links {
    display: flex;
    flex-direction: column;
}

.footer-link-group {
    display: flex;
    flex-direction: column;
}

.footer-link-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 16px;
}

.footer-link {
    color: #666;
    text-decoration: none;
    font-size: 14px;
    padding: 8px 0;
    transition: all 0.2s ease;
    display: inline-block;

    &:hover {
        color: var(--q-primary);
        transform: translateX(4px);
    }
}

.footer-contact {
    display: flex;
    flex-direction: column;
}

.footer-contact-item {
    display: flex;
    align-items: center;
    color: #666;
    font-size: 14px;
    padding: 8px 0;
    transition: color 0.2s ease;

    &:hover {
        color: var(--q-primary);
    }
}

.footer-separator {
    margin: 24px 0;
    background: #e0e0e0;
}

.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
}

.footer-copyright {
    display: flex;
    align-items: center;
    color: #666;
    font-size: 14px;
}

.footer-social {
    display: flex;
    gap: 8px;
}

.social-btn {
    transition: all 0.3s ease;

    &:hover {
        background-color: #f5f5f5;
        transform: scale(1.1);
    }
}

@media (max-width: 768px) {
    .footer-section {
        grid-template-columns: 1fr 1fr;
        gap: 32px;
    }

    .footer-contact {
        grid-column: 1 / -1;
    }
}

@media (max-width: 600px) {
    .footer-content {
        padding: 32px 16px 20px;
    }

    .footer-section {
        grid-template-columns: 1fr;
        gap: 24px;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
}
</style>
