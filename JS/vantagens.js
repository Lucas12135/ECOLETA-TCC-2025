// JavaScript para interatividade na página de Vantagens

document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('.benefits-grid');
    const dots = document.querySelectorAll('.dot');
    
    if (!grid || dots.length === 0) return;

    // Função para atualizar os pontos (dots) com base na posição do scroll
    const updateDots = () => {
        const scrollLeft = grid.scrollLeft;
        const cardWidth = grid.querySelector('.benefit-card').offsetWidth + 24; // Largura do card + gap (1.5rem * 16px = 24px)
        
        // Calcula o índice do card que está mais visível
        let activeIndex = Math.round(scrollLeft / cardWidth);

        // Limita o índice para o último card
        if (activeIndex >= dots.length) {
            activeIndex = dots.length - 1;
        }

        dots.forEach((dot, index) => {
            dot.classList.remove('active');
            if (index === activeIndex) {
                dot.classList.add('active');
            }
        });
    };

    // Adiciona evento de scroll para atualizar os pontos
    grid.addEventListener('scroll', updateDots);

    // Adiciona evento de clique nos pontos para rolar para o card correspondente
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            const cardWidth = grid.querySelector('.benefit-card').offsetWidth + 24;
            // Rola para a posição do card clicado
            grid.scrollTo({
                left: index * cardWidth,
                behavior: 'smooth'
            });
        });
    });

    // Chama updateDots na inicialização
    updateDots();
});
