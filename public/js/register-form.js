document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-register-form]');

    if (!form) {
        return;
    }

    const steps = Array.from(form.querySelectorAll('.register-step'));
    const indicators = Array.from(document.querySelectorAll('.step-indicator'));
    const progressBar = document.getElementById('registrationProgressBar');
    const backButton = document.getElementById('registerBackButton');
    const nextButton = document.getElementById('registerNextButton');
    const submitButton = document.getElementById('registerSubmitButton');
    const baptismStatusInputs = Array.from(form.querySelectorAll('input[name="baptism_status"]'));
    const catechismSection = document.getElementById('catechismSection');
    const baptizedInfoSection = document.getElementById('baptizedInfoSection');
    const catechismRequiredFields = Array.from(form.querySelectorAll('[data-catechism-required]'));
    let currentStep = 1;

    const updateStepUI = () => {
        steps.forEach((step) => {
            const stepNumber = Number(step.dataset.step);
            step.classList.toggle('hidden', stepNumber !== currentStep);
        });

        indicators.forEach((indicator) => {
            const stepNumber = Number(indicator.dataset.stepIndicator);
            const circle = indicator.querySelector('span');
            const label = indicator.querySelectorAll('span')[1];
            const isActive = stepNumber === currentStep;
            const isCompleted = stepNumber < currentStep;

            indicator.className = `step-indicator flex items-center gap-3 rounded-lg border p-3 ${
                isActive ? 'border-blue-100 bg-blue-50' : 'border-slate-200 bg-white'
            }`;
            circle.className = `inline-flex h-7 w-7 items-center justify-center rounded-full text-sm font-semibold text-white ${
                isActive || isCompleted ? 'bg-blue-700' : 'bg-slate-300'
            }`;
            label.className = `text-sm font-medium ${isActive ? 'text-blue-900' : 'text-slate-700'}`;
        });

        backButton.classList.toggle('invisible', currentStep === 1);
        nextButton.classList.toggle('hidden', currentStep === steps.length);
        submitButton.classList.toggle('hidden', currentStep !== steps.length);

        if (progressBar) {
            progressBar.style.width = `${Math.round((currentStep / steps.length) * 100)}%`;
        }
    };

    const validateCurrentStep = () => {
        const activeStep = steps.find((step) => Number(step.dataset.step) === currentStep);

        if (!activeStep) {
            return true;
        }

        const fields = Array.from(activeStep.querySelectorAll('input, select, textarea'))
            .filter((field) => !field.disabled && field.type !== 'hidden');

        for (const field of fields) {
            if (!field.checkValidity()) {
                field.reportValidity();

                return false;
            }
        }

        return true;
    };

    const updateBaptismSections = () => {
        const selected = baptismStatusInputs.find((input) => input.checked)?.value;
        const showCatechism = selected === 'belum_dibaptis';
        const showBaptizedInfo = selected === 'sudah_dibaptis';

        if (catechismSection) {
            catechismSection.classList.toggle('hidden', !showCatechism);
        }

        if (baptizedInfoSection) {
            baptizedInfoSection.classList.toggle('hidden', !showBaptizedInfo);
        }

        catechismRequiredFields.forEach((field) => {
            field.required = showCatechism;
            field.disabled = !showCatechism;
        });
    };

    backButton?.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep -= 1;
            updateStepUI();
        }
    });

    nextButton?.addEventListener('click', () => {
        if (!validateCurrentStep()) {
            return;
        }

        if (currentStep < steps.length) {
            currentStep += 1;
            updateStepUI();
        }
    });

    baptismStatusInputs.forEach((input) => {
        input.addEventListener('change', updateBaptismSections);
    });

    updateBaptismSections();
    updateStepUI();
});
