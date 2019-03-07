<?php
/**
 * Created by PhpStorm.
 * User: Samurai3095
 * Date: 2/13/2019
 * Time: 10:22 AM
 */

namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    /**
     * @var AccessDecisionManagerInterface
     */
    private $manager;

    public function __construct(AccessDecisionManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute,[self::EDIT, self::DELETE])){
            return false;
        }
        if(!$subject instanceof MicroPost){
            return false;
        }
        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if($this->manager->decide($token,[User::ROLE_ADMIN])){
            return true;
        }
        $authen = $token->getUser();
        if(!$authen instanceof User){
            return false;
        }
        /**
         * @var MicroPost $microPost
         */
        $microPost = $subject;

        return $microPost->getUser()->getId() == $authen->getId();
    }
}