<!-- app/Views/partials/whatsapp-float.php -->
<!-- WhatsApp Floating Button -->
<a href="https://wa.me/6281234567890" class="whatsapp-float" target="_blank" title="Hubungi Kami via WhatsApp">
    <i class="fab fa-whatsapp whatsapp-float-icon"></i>
</a>

<style>
.whatsapp-float {
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 40px;
    background-color: #25d366;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 3px #999;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.whatsapp-float:hover {
    background-color: #128C7E;
    color: #FFF;
    transform: scale(1.1);
    text-decoration: none;
}

@media (max-width: 768px) {
    .whatsapp-float {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
        font-size: 25px;
    }
}
</style>