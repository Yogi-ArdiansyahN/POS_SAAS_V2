<?php

namespace App\Filters;

use App\Models\Merchants;
use App\Models\CookiesModel;
use App\Models\Provinsi;
use App\Models\UsersModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CheckCompanies implements FilterInterface
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
        $merchant = new Merchants();
        $usersModel = new UsersModel();

        $users = session()->get('users');

        // check session
        if (empty($users)) {
            return redirect()->to(base_url());
        }

        $check_companies = $merchant->where('users_id', $users['id'])->first();
        // dd($check_companies);
        // check companies
        if (empty($check_companies)) {
            return redirect()->to('merchant');
        }

        // check users companies
        if (empty($users['merchants_id'])) {
            $users_id = $users['id'];
            $manajemen_companies = $check_companies['users_id'];

            if ($users_id === $manajemen_companies) {
                $usersModel->set(['merchants_id' => $check_companies['id']])->where('id', $users['id'])->update();
            }

            session()->destroy();
            setcookie('pos_saas', '', 1, '/');
            session()->setFlashdata('success', "Berhasil Logout");
            return redirect()->to('/');
        }

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
