// script.js

document.addEventListener('DOMContentLoaded', function () {
  const checkStatusBtn = document.getElementById('checkStatusBtn');
  const responseContainer = document.getElementById('responseContainer');
  const responseCode = document.getElementById('responseCode');
  const knowledgeBaseTip = document.getElementById('knowledgeBaseTip');

  checkStatusBtn.addEventListener('click', async () => {
    const apiLogin = document.getElementById('apiLogin').value.trim();
    const organizationId = document.getElementById('organizationId').value.trim();
    const correlationId  = document.getElementById('correlationId').value.trim();

    // Минимальная проверка
    if (!apiLogin) {
      alert('Пожалуйста, выберите заведение (apiLogin).');
      return;
    }
    if (!organizationId) {
      alert('Пожалуйста, введите Organization ID.');
      return;
    }
    if (!correlationId) {
      alert('Пожалуйста, введите Correlation ID.');
      return;
    }

    // Готовим FormData для отправки
    const formData = new FormData();
    formData.append('apiLogin', apiLogin);
    formData.append('organizationId', organizationId);
    formData.append('correlationId', correlationId);

    try {
      const response = await fetch('process.php', {
        method: 'POST',
        body: formData
      });

      // Если код ответа HTTP не в диапазоне 200-299
      if (!response.ok) {
        throw new Error('Сервер вернул ошибку: ' + response.status);
      }

      const data = await response.json();

      // Покажем блок с результатами
      responseContainer.style.display = 'block';

      // Форматируем JSON красиво
      const jsonString = JSON.stringify(data.apiResponse, null, 2) || 'Нет данных.';
      responseCode.textContent = jsonString;

      // Подсветка highlight.js
      hljs.highlightElement(responseCode);

      // Если нашлась подсказка из базы знаний
      if (data.knowledgeBase) {
        knowledgeBaseTip.innerHTML = `<strong>Подсказка:</strong> ${data.knowledgeBase}`;
      } else {
        knowledgeBaseTip.innerHTML = '';
      }
    } catch (error) {
      alert('Ошибка при запросе: ' + error);
    }
  });
});
