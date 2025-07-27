<?php

namespace App\Filters;

use App\Models\CookiesModel;
use App\Models\Langganans;
use App\Models\Mitras;
use App\Models\RiwayatLangganans;
use App\Models\Users;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class IsLangganan implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('text');
        $uri = service('uri');
        $segment = $uri->getSegment(1);

        $riwayat_langganans = new RiwayatLangganans();

        $session_user = session()->get('users');

        if (empty($session_user)) {
            session()->setFlashdata('errors', 'Silahkan login terlebih dahulu.');
            return redirect()->to(base_url('/'));
        }

        $riwayat = $riwayat_langganans->where('mitras_id', $session_user['mitras_id'])->where('status', 'lunas')->first();

        if ($session_user['role'] == 'mitra') {
            if (!$riwayat) {
                session()->setFlashdata('errors', 'Tolong pilih langganan terlebih dahulu sebelum mengakases fitur lain.');
                return redirect()->to(base_url() . 'mitra/langganan');
            }
        }
        // else if ($session_user['role'] == 'kasir') {
        //     if (!$riwayat) {
        //         session()->destroy();
        //         // unset($_COOKIE['estetik_cookies']);
        //         setcookie('pos_saas', '', 1, '/');
        //         session()->setFlashdata('errors', 'Tolong pilih langganan terlebih dahulu sebelum mengakases fitur lain.');
        //         return redirect()->to('/');
        //     }
        // }



        return;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
