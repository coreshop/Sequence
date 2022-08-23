<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Component\Sequence\Generator;

use CoreShop\Component\Sequence\Factory\SequenceFactoryInterface;
use CoreShop\Component\Sequence\Model\SequenceInterface;
use CoreShop\Component\Sequence\Repository\SequenceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class SequenceGenerator implements SequenceGeneratorInterface
{
    public function __construct(private SequenceRepositoryInterface $sequenceRepository, private SequenceFactoryInterface $sequenceFactory, private EntityManagerInterface $entityManager)
    {
    }

    public function getNextSequenceForType(string $type): int
    {
        $sequence = $this->getSequence($type);
        $sequence->incrementIndex();

        $this->entityManager->persist($sequence);
        $this->entityManager->flush();

        return $sequence->getIndex();
    }

    private function getSequence(string $type): SequenceInterface
    {
        $sequence = $this->sequenceRepository->findForType($type);

        if (null === $sequence) {
            $sequence = $this->sequenceFactory->createWithType($type);
            $this->sequenceRepository->add($sequence);
        }

        return $sequence;
    }
}
