<?php

namespace App\Controller;

use App\Entity\CadastroPessoas;
use App\Form\BuildFormType;
use App\Repository\CadastroPessoasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CadastroController extends AbstractController
{
    /**
     * @Route("/", name="app_cadastro", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(BuildFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pessoa = $form->getData();

            $senha = $pessoa->getPassword();

            if (!$this->validateSenha($senha)) {
                return $this->json(['error' => 'A senha não atende aos requisitos.'], 400);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pessoa);
            $entityManager->flush();

            $this->addFlash('success', 'Cadastro salvo com sucesso!');

            return $this->redirectToRoute('app_cadastro');
        }

        $pessoas = $this->getDoctrine()->getRepository(CadastroPessoas::class)->findAll();

        return $this->render('cadastroPessoas/index.html.twig', [
            'form' => $form->createView(),
            'persons' => $pessoas,
        ]);
    }

    private function validateSenha(string $senha): bool
    {
        return true;
    }

    /**
     * @Route("/lista_registros", name="lista_registros")
     */
    public function listaRegistros(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository(CadastroPessoas::class)->createQueryBuilder('p');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        foreach ($pagination as $person) {
            $person->faixaEtaria = $this->getFaixaEtaria($person->getDataNascimento());
        }

        return $this->render('cadastroPessoas/lista_registros.html.twig', [
            'persons' => $pagination,
        ]);
    }

    private function getFaixaEtaria(\DateTime $dataNascimento): string
    {
        $idade = (new \DateTime('now'))->diff($dataNascimento)->y;

        if ($idade < 2) {
            return 'Bebê';
        } elseif ($idade < 12) {
            return 'Criança';
        } elseif ($idade < 18) {
            return 'Adolescente';
        } elseif ($idade < 30) {
            return 'Jovem';
        } elseif ($idade < 60) {
            return 'Adulto';
        } else {
            return 'Idoso';
        }
    }

    /**
     * @Route("/cadastro/alterar/{id}", name="alterar_pessoa", methods={"GET", "POST"})
     */
    public function alterarPessoa(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $pessoa = $entityManager->getRepository(CadastroPessoas::class)->find($id);

        if (!$pessoa) {
            throw $this->createNotFoundException('Pessoa não encontrada');
        }

        $form = $this->createForm(BuildFormType::class, $pessoa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('lista_registros');
        }

        return $this->render('cadastroPessoas/alterar_pessoa.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete_pessoa/{id}", name="delete_pessoa")
     */
    public function deletePessoa(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $pessoa = $entityManager->getRepository(CadastroPessoas::class)->find($id);

        if (!$pessoa) {
            throw $this->createNotFoundException('Pessoa não encontrada');
        }

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('delete_pessoa', ['id' => $pessoa->getId()]))
            ->add('confirmar', SubmitType::class, ['label' => 'Confirmar Exclusão', 'attr' => ['class' => 'btn btn-danger mr-2']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pessoa->setDeletado(true);
            $entityManager->flush();

            $this->addFlash('success', 'Pessoa excluída com sucesso.');

            return $this->redirectToRoute('lista_registros');
        }

        return $this->render('cadastroPessoas/delete_confirmation.html.twig', [
            'form' => $form->createView(),
            'pessoa' => $pessoa,
        ]);
    }

    /**
     * @Route("/cadastro/deletePessoa/{id}/confirmacao", name="delete_pessoa_confirmation", methods={"GET"})
     */
    public function deleteConfirmation($id): Response
    {
        $cadastroPessoasRepository = $this->getDoctrine()->getRepository(CadastroPessoas::class);
        $cadastroPessoas = $cadastroPessoasRepository->find($id);

        if (!$cadastroPessoas) {
            return $this->json(['error' => 'Pessoa não encontrada.'], 404);
        }

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('delete_pessoa', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm();

        return $this->render('cadastroPessoas/delete_confirmation.html.twig', [
            'pessoa' => $cadastroPessoas,
            'form' => $form->createView(),
        ]);
    }
}
