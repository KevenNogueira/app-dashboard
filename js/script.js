$('#documentacao').on('click', () => {
  //$('#pagina').load('documentacao.html')

  /*
		$.get('documentacao.html', data => { 
			$('#pagina').html(data)
		})
		*/
  $.post('documentacao.html', (data) => {
    $('#pagina').html(data);
  });
});

$('#suporte').on('click', () => {
  //$('#pagina').load('suporte.html')

  /*  
		$.get('suporte.html', data => { 
			$('#pagina').html(data)
		})
		*/

  $.post('suporte.html', (data) => {
    $('#pagina').html(data);
  });
});

$('#dashboard').on('click', () => {
  $.post('index.html', (data) => {
    $('body').html(data);
  });
});

$('#competencia').on('change', (e) => {
  let competencia = $(e.target).val();

  $.ajax({
    type: 'GET',
    url: 'app.php',
    data: `competencia=${competencia}`,
    dataType: 'json',
    success: (dados) => {
      let vlr_vendas = formtCurrency(dados.valorVendas);
      let vlr_despesa = formtCurrency(dados.valorDespesa);
      $('#num_venda').html(dados.numeroVendas);
      $('#vlr_vendas').html(vlr_vendas);
      $('#cli_ativo').html(dados.clienteAtivo);
      $('#cli_ativo').html(dados.clienteAtivo);
      $('#cli_inativo').html(dados.clienteInativo);
      $('#reclamacoes').html(dados.numeroCriticas);
      $('#elogios').html(dados.numeroElogios);
      $('#sugestoes').html(dados.numeroSugestoes);
      $('#despesas').html(vlr_despesa);
      console.log(dados);
    },
    error: (error) => {
      $('#pagina').html(error);
    },
  });
});

function formtCurrency(vlr) {
  let numero = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(vlr);

  return numero;
}
