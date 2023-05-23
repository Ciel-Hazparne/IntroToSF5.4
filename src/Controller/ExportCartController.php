<?php


namespace App\Controller;


use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class ExportCartController extends CartController
{
    #[Route('/cart/pdf', name: 'cart_pdf')]
    public function pdf(SessionInterface $session, ArticleRepository $articleRepository): void
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instancie Dompdf avec nos options
        $dompdf = new Dompdf($pdfOptions);

        $cart = $session->get('cart', []);
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'id'=>$id,
                'article' => $articleRepository->find($id),
                'quantity' => $quantity
            ];
        }

//        dd($cartWithData);
        $html = $this->render('cart/pdf.html.twig', [
            'items' => $cartWithData
        ]);
        // Dompdf récupère le HTML généré
        $dompdf->loadHtml($html);

        // (Optionnel) mise en page
        $dompdf->setPaper('A4', 'portrait');

        // HTML mis en PDF
        $dompdf->render();

        // générer le pdf sous forme de fichier à télécharger ("Attachment" => true) si false le pdf s'ouvre dans le navigateur
        $dompdf->stream("liste.pdf", [
            "Attachment" => true
        ]);
    }

}