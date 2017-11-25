<?php
namespace Rend\Helper\Http;

use Zend\Stdlib\RequestInterface;

trait Range
{
    /**
     * Split up Range HTTP header or return default.
     *
     * @param RequestInterface $request
     * @param int $count
     * @param int $perPage
     * @return RangeValue
     * @todo if range is something like 'items hundur-vei'
     */
    private function getRange(RequestInterface $request, $count = 0, $perPage = 25): RangeValue
    {
        /** @var $range \Zend\Http\Header\Range */
        if ($range = $request->getHeader('Range')) {
            $match = [];
            preg_match('/([0-9]*)-([0-9]*)?/', $range->getFieldValue(), $match);

            $from = is_numeric($match[1]) ? (int) $match[1] : 0;
            $to = is_numeric($match[2]) ? (int) $match[2] : null;

            if ($to === null) {
                return (new RangeValue())
                    ->setFrom($from)
                    ->setTo(null);
            } else {
                //NEGATIVE RANGE
                if ($to - $from < 0) {
                    return (new RangeValue())
                        ->setFrom(0)
                        ->setTo(0);
                //OUT OF RANGE
                } elseif ($to > $count) {
                    //BOTH OUT OF RANGE
                    if ($from > $count) {
                        return (new RangeValue())
                            ->setFrom(0)
                            ->setTo(0);
                    }
                    //LOWER BOUND IN RANGE
                    return (new RangeValue())
                        ->setFrom($from)
                        ->setTo($count);
                //RANGE BIGGER
                } elseif ($to - $from > $perPage) {
                    return (new RangeValue())
                        ->setFrom($from)
                        ->setTo($from + $perPage);

                }

                return (new RangeValue())
                    ->setFrom($from)
                    ->setTo($to);
            }

        } else {
            return (new RangeValue())
                ->setFrom(0)
                ->setTo(null);
        }
    }
}
