
document.addEventListener('DOMContentLoaded', function () {
  // Máscara CPF
  document.querySelectorAll('.cpf').forEach(function (el) {
      el.addEventListener('input', function () {
          let value = el.value.replace(/\D/g, '');
          if (value.length > 11) value = value.slice(0, 11);
          value = value.replace(/(\d{3})(\d)/, '$1.$2');
          value = value.replace(/(\d{3})(\d)/, '$1.$2');
          value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
          el.value = value;
      });
  });

  // Máscara Telefone
  document.querySelectorAll('.telefone').forEach(function (el) {
      el.addEventListener('input', function () {
          let value = el.value.replace(/\D/g, '');
          if (value.length > 11) value = value.slice(0, 11);
          if (value.length <= 10) {
              value = value.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
          } else {
              value = value.replace(/^(\d{2})(\d{5})(\d{0,4})$/, '($1) $2-$3');
          }
          el.value = value;
      });
  });

  // Máscara Data (DD/MM/AAAA)
  document.querySelectorAll('.data').forEach(function (el) {
      el.addEventListener('input', function () {
          let value = el.value.replace(/\D/g, '');
          if (value.length > 8) value = value.slice(0, 8);
          value = value.replace(/(\d{2})(\d)/, '$1/$2');
          value = value.replace(/(\d{2})(\d)/, '$1/$2');
          el.value = value;
      });
  });
});


// Função para garantir que só números sejam inseridos
function allowOnlyNumbers(event) {
    // Pega o valor do input
    let value = event.target.value;    
    // Substitui tudo que não for número por uma string vazia
    value = value.replace(/\D/g, '');    
    // Atualiza o valor do input
    event.target.value = value;
  }  
  // Adiciona o evento de input para todos os campos com a classe 'number'
  document.querySelectorAll('.number').forEach(input => {
    input.addEventListener('input', allowOnlyNumbers);
  });
  

