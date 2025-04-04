function typingText(texts) {
   
    console.log(texts, "Texts");

    let textIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const speed = 100;
    const delay = 2000;
    const typingElement = document.getElementById("typing-text");

    function type() {
        const currentText = texts[textIndex];

        if (isDeleting) {
            typingElement.innerHTML = currentText.substring(0, charIndex) + "<span class='blinking'>|</span>";
            charIndex--;
        } else {
            typingElement.innerHTML = currentText.substring(0, charIndex + 1) + "<span class='blinking'>|</span>";
            charIndex++;
        }

        if (!isDeleting && charIndex === currentText.length) {
            isDeleting = true;
            setTimeout(type, delay);
        } else if (isDeleting && charIndex === -1) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
            setTimeout(type, speed);
        } else {
            setTimeout(type, isDeleting ? speed / 2 : speed);
        }
    }

    type();
}
