@extends('site.layouts.app')
@section('content')

<section class="bg-beige py-10" style="padding-bottom: 100px;">
  <div class="container px-5">
    <div class="mb-5" style="padding-top: 100px;">
      <h2>Política de privacidad</h2>
      <p class="lead">Esta Política de Privacidad describe cómo se recopila, utiliza y comparte la información personal de los usuarios ("usted" o "usuario") del sitio web [Nombre del Sitio Web] ("Sitio") y los servicios relacionados ofrecidos por [Nombre de la Empresa] ("nosotros", "nuestro" o "nosotros").</p>
        <br>
        <b>Recopilación de Información Personal</b>
      <p class="lead">Podemos recopilar información personal identificable del usuario de varias maneras, incluyendo, pero no limitado a, cuando el usuario visita nuestro Sitio, se registra en el Sitio, realiza un pedido, suscribe un boletín informativo, responde a una encuesta, completa un formulario o interactúa con otras funciones del Sitio. La información personal que podemos recopilar puede incluir, entre otros, el nombre, la dirección de correo electrónico, la dirección postal, el número de teléfono y la información de tarjeta de crédito del usuario.</p>
      <br>
      <b>Uso de la Información Recopilada</b>
      <p class="lead">Podemos utilizar la información personal del usuario para los siguientes propósitos:</p>
      <ul  class="lead">
        <li>Personalizar la experiencia del usuario y permitirnos ofrecer el tipo de contenido y ofertas de productos en los que está más interesado.</li>
        <li>Mejorar nuestro Sitio y nuestros servicios para brindar un mejor servicio al cliente.</li>
        <li>Procesar transacciones rápidamente.</li>
        <li>Enviar correos electrónicos periódicos sobre su pedido u otros productos y servicios.</li>
        <li>Administrar una encuesta o promoción o características del Sitio.</li>
        <li>Brindarle noticias, ofertas especiales y otra información relacionada con nuestro negocio que consideremos que puede ser de su interés.</li>
      </ul>
      <br>
      <b>Protección de la Información del Usuario</b>
      <p class="lead">Adoptamos prácticas de recopilación de datos adecuadas, almacenamiento y procesamiento y medidas de seguridad para proteger contra el acceso no autorizado, alteración, divulgación o destrucción de su información personal, nombre de usuario, contraseña, información de transacciones y datos almacenados en nuestro Sitio.</p>
      <br>
      <b>Compartir Información Personal</b>
      <p class="lead">No vendemos, intercambiamos ni alquilamos información personal de usuarios a terceros. Podemos compartir información demográfica agregada genérica no vinculada a información de identificación personal sobre los visitantes y usuarios con nuestros socios comerciales, afiliados confiables y anunciantes para los fines mencionados anteriormente.</p>
      <br>
      <b>Cambios en la Política de Privacidad</b>
      <p class="lead">Nos reservamos el derecho de actualizar esta política de privacidad en cualquier momento. Se recomienda a los usuarios que verifiquen esta página con frecuencia para mantenerse informados sobre cómo estamos protegiendo la información personal que recopilamos. Usted reconoce y acepta que es su responsabilidad revisar esta política de privacidad periódicamente y tomar conciencia de las modificaciones.</p>
      <br>
      <b>Aceptación de los Términos</b>
      <p class="lead">Al utilizar este Sitio, usted indica su aceptación de esta política. Si no está de acuerdo con esta política, por favor no use nuestro Sitio. Su uso continuado del Sitio después de la publicación de cambios en esta política se considerará su aceptación de dichos cambios.</p>
      <br>
      <b>Contacto</b>
      <p class="lead">Si tiene alguna pregunta sobre esta Política de Privacidad, las prácticas de este sitio o sus relaciones con este sitio, contáctenos en:</p>
      <br>
      <ul  class="lead">
        <li>Plohn Petersen</li>
        <li>Avenida España 2287 1207 Asunción, Paraguay</li>
        <li>fp@pyp.com.py</li>
        <li>0984 309337</li>
      </ul>
      <br>
      <br>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-12" style="text-align: center;margin-bottom: 40px;">
      <a href="{{ url('/user/register') }}">
        <button style="font-size: 18px;" class="main-btn info-btn btn-hover btn-sm">Atrás</button>
      </a>
    </div>
  </div>
</section>
@endsection