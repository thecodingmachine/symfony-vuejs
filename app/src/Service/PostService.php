<?php

namespace App\Service;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

final class PostService
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * PostService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $message
     * @return Post
     */
    public function createPost(string $message): Post
    {
        $postEntity = new Post();
        $postEntity->setMessage($message);
        $this->em->persist($postEntity);
        $this->em->flush();

        return $postEntity;
    }

    /**
     * @return object[]
     */
    public function getAll(): array
    {
        return $this->em->getRepository(Post::class)->findBy([], ['id' => 'DESC']);
    }
}
