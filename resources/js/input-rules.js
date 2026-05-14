const CONTROL_SELECTOR = 'input:not([type="hidden"]):not([type="file"]):not([type="checkbox"]):not([type="radio"])';
const EXCLUDED_TYPES = new Set(['button', 'color', 'date', 'datetime-local', 'email', 'month', 'password', 'search', 'submit', 'time', 'url', 'week']);

function identityFor(input) {
    return [
        input.name,
        input.id,
        input.getAttribute('autocomplete'),
        input.getAttribute('placeholder'),
    ]
        .filter(Boolean)
        .join(' ')
        .toLowerCase();
}

function setDefaultAttribute(input, name, value) {
    if (!input.hasAttribute(name)) {
        input.setAttribute(name, value);
    }
}

function onlyDigits(value, maxLength = null) {
    const digits = String(value ?? '').replace(/\D+/g, '');
    return maxLength ? digits.slice(0, maxLength) : digits;
}

function normalizeDecimal(value, decimals = 2) {
    const cleaned = String(value ?? '').replace(/[^\d.]/g, '');
    const [integerPart, ...decimalParts] = cleaned.split('.');
    const decimalPart = decimalParts.join('').slice(0, decimals);

    if (cleaned.includes('.')) {
        return `${integerPart || '0'}.${decimalPart}`;
    }

    return integerPart;
}

function isPhoneField(identity) {
    return ['phone', 'mobile', 'contact', 'gcash'].some((token) => identity.includes(token));
}

function isCardNumberField(identity) {
    return /(card[_\s-]*number|cc[_\s-]*number|credit[_\s-]*card|debit[_\s-]*card)/.test(identity);
}

function isReferenceField(identity) {
    return identity.includes('reference') ||
        /\btransaction[_\s-]*(id|number)\b|\bref[_\s-]*(id|number)\b/.test(identity);
}

function isAmountField(identity) {
    return ['amount', 'price', 'fee', 'cost', 'goal', 'donation'].some((token) => identity.includes(token));
}

function isCoordinateField(identity) {
    return ['lat', 'lng', 'latitude', 'longitude', 'coordinate', 'coords'].some((token) => identity.includes(token));
}

function isDecimalMeasurementField(identity) {
    return ['hectare', 'hectares', 'weight', 'kg', 'kilo', 'kilogram', 'length', 'width', 'height'].some((token) => identity.includes(token));
}

function isIntegerField(input, identity) {
    if (isCoordinateField(identity) || isAmountField(identity) || isDecimalMeasurementField(identity)) {
        return false;
    }

    if (['age', 'quantity', 'qty', 'count', 'household', 'individual', 'population', 'livestock', 'male', 'female', 'children', 'elderly', 'pwd', 'pwds', 'houses'].some((token) => identity.includes(token))) {
        return true;
    }

    return input.type === 'number' && !String(input.step || '').includes('.');
}

function luhnPasses(number) {
    let sum = 0;
    let shouldDouble = false;

    for (let index = number.length - 1; index >= 0; index -= 1) {
        let digit = Number(number[index]);

        if (shouldDouble) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }

        sum += digit;
        shouldDouble = !shouldDouble;
    }

    return sum % 10 === 0;
}

function expectedCardLengths(number) {
    if (/^3[47]/.test(number)) {
        return [15];
    }

    if (/^4/.test(number)) {
        return [13, 16, 19];
    }

    if (/^(5[1-5]|2(2[2-9][1-9]|[3-6]\d{2}|7[01]\d|720))/.test(number)) {
        return [16];
    }

    if (/^(6011|65|64[4-9])/.test(number)) {
        return [16, 19];
    }

    if (/^35/.test(number)) {
        return [16, 17, 18, 19];
    }

    return [13, 14, 15, 16, 17, 18, 19];
}

function getRule(input) {
    const identity = identityFor(input);

    if (EXCLUDED_TYPES.has(input.type) || input.readOnly || input.disabled) {
        return null;
    }

    if (isCardNumberField(identity)) {
        return 'card';
    }

    if (isPhoneField(identity)) {
        return 'phone';
    }

    if (isReferenceField(identity)) {
        return 'reference';
    }

    if (isAmountField(identity)) {
        return 'amount';
    }

    if (isIntegerField(input, identity)) {
        return 'integer';
    }

    if (input.type === 'number' || isDecimalMeasurementField(identity)) {
        return 'decimal';
    }

    return null;
}

function applyAttributes(input, rule) {
    if (rule === 'phone') {
        input.type = 'tel';
        input.inputMode = 'numeric';
        setDefaultAttribute(input, 'maxlength', '11');
        setDefaultAttribute(input, 'pattern', '09[0-9]{9}');
        setDefaultAttribute(input, 'autocomplete', 'tel');
    }

    if (rule === 'card') {
        input.type = 'text';
        input.inputMode = 'numeric';
        setDefaultAttribute(input, 'maxlength', '19');
        setDefaultAttribute(input, 'autocomplete', 'cc-number');
    }

    if (rule === 'reference') {
        input.type = 'text';
        input.inputMode = 'numeric';
        setDefaultAttribute(input, 'maxlength', '30');
        setDefaultAttribute(input, 'pattern', '[0-9]{5,30}');
    }

    if (rule === 'amount' || rule === 'decimal') {
        input.inputMode = 'decimal';
        setDefaultAttribute(input, 'min', rule === 'amount' ? '1' : '0');
    }

    if (rule === 'integer') {
        input.inputMode = 'numeric';
        setDefaultAttribute(input, 'min', '0');
        setDefaultAttribute(input, 'step', '1');
        setDefaultAttribute(input, 'pattern', '[0-9]*');
    }
}

function normalizeInputValue(input, rule) {
    const original = input.value;
    let normalized = original;

    if (rule === 'phone') {
        normalized = onlyDigits(original, 11);
    } else if (rule === 'card') {
        normalized = onlyDigits(original, 19);
    } else if (rule === 'reference') {
        normalized = onlyDigits(original, Number(input.getAttribute('maxlength')) || 30);
    } else if (rule === 'amount') {
        normalized = normalizeDecimal(original, 2);
    } else if (rule === 'integer') {
        normalized = onlyDigits(original, Number(input.getAttribute('maxlength')) || null);
    } else if (rule === 'decimal') {
        normalized = normalizeDecimal(original, 4);
    }

    if (normalized !== original) {
        input.value = normalized;
    }
}

function setRangeValidity(input) {
    const value = input.value;

    if (value === '') {
        input.setCustomValidity('');
        return;
    }

    const number = Number(value);
    const min = input.hasAttribute('min') ? Number(input.min) : null;
    const max = input.hasAttribute('max') ? Number(input.max) : null;

    if (Number.isNaN(number)) {
        input.setCustomValidity('Enter numbers only.');
    } else if (min !== null && number < min) {
        input.setCustomValidity(`Enter a value of at least ${input.min}.`);
    } else if (max !== null && number > max) {
        input.setCustomValidity(`Enter a value of at most ${input.max}.`);
    } else {
        input.setCustomValidity('');
    }
}

function validateInput(input, rule) {
    const value = input.value.trim();

    if (rule === 'phone') {
        input.setCustomValidity(value === '' || /^09\d{9}$/.test(value)
            ? ''
            : 'Enter an 11-digit Philippine mobile number starting with 09.');
        return input.validationMessage === '';
    }

    if (rule === 'card') {
        const lengths = expectedCardLengths(value);
        input.setCustomValidity(value === '' || (lengths.includes(value.length) && luhnPasses(value))
            ? ''
            : `Enter a valid card number with ${lengths.join(' or ')} digits.`);
        return input.validationMessage === '';
    }

    if (rule === 'reference') {
        input.setCustomValidity(value === '' || /^\d{5,30}$/.test(value)
            ? ''
            : 'Enter digits only, 5 to 30 numbers long.');
        return input.validationMessage === '';
    }

    if (rule === 'integer') {
        input.setCustomValidity(value === '' || /^\d+$/.test(value) ? '' : 'Enter whole numbers only.');
        if (input.validationMessage === '') {
            setRangeValidity(input);
        }
        return input.validationMessage === '';
    }

    if (rule === 'amount' || rule === 'decimal') {
        input.setCustomValidity(value === '' || /^\d+(\.\d{0,2})?$/.test(value)
            ? ''
            : 'Enter numbers only.');
        if (input.validationMessage === '') {
            setRangeValidity(input);
        }
        return input.validationMessage === '';
    }

    return true;
}

function bindInput(input) {
    if (!(input instanceof HTMLInputElement) || input.dataset.tkInputRulesBound === 'true') {
        return;
    }

    const rule = getRule(input);

    if (!rule) {
        return;
    }

    input.dataset.tkInputRulesBound = 'true';
    input.dataset.tkInputRule = rule;
    applyAttributes(input, rule);
    normalizeInputValue(input, rule);
    validateInput(input, rule);

    input.addEventListener('keydown', (event) => {
        if (event.ctrlKey || event.metaKey || event.altKey) {
            return;
        }

        if (['e', 'E', '+', '-'].includes(event.key)) {
            event.preventDefault();
        }

        if ((rule === 'phone' || rule === 'card' || rule === 'reference' || rule === 'integer') && event.key === '.') {
            event.preventDefault();
        }
    });

    input.addEventListener('input', () => {
        normalizeInputValue(input, rule);
        validateInput(input, rule);
    });

    input.addEventListener('blur', () => {
        normalizeInputValue(input, rule);
        validateInput(input, rule);
    });
}

function bindInputs(root = document) {
    if (root instanceof HTMLInputElement) {
        bindInput(root);
    }

    root.querySelectorAll?.(CONTROL_SELECTOR).forEach(bindInput);
}

function validateForm(form) {
    const controlledInputs = Array.from(form.querySelectorAll('[data-tk-input-rule]'));
    const firstInvalid = controlledInputs.find((input) => !validateInput(input, input.dataset.tkInputRule));

    if (firstInvalid) {
        firstInvalid.reportValidity();
        return false;
    }

    return true;
}

function initInputRules() {
    bindInputs();

    document.addEventListener('submit', (event) => {
        if (event.target instanceof HTMLFormElement && !validateForm(event.target)) {
            event.preventDefault();
        }
    }, true);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node instanceof Element) {
                    bindInputs(node);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initInputRules, { once: true });
} else {
    initInputRules();
}
