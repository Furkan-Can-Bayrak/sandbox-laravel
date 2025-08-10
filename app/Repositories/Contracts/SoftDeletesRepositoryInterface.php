<?php

namespace App\Repositories\Contracts;

use App\Repositories\Criteria\QueryParameters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Soft Deletes özelliğini kullanan repository'ler için temel sözleşme (contract).
 *
 * Bu arayüz; aktif + silinmiş kayıtları birlikte veya ayrı ayrı listelemek,
 * silinmiş bir kaydı geri yüklemek ve bir kaydı kalıcı olarak silmek için
 * gerekli metotları tanımlar.
 *
 * Gelişmiş sorgular, QueryParameters nesnesi aracılığıyla yapılır ve bu nesne,
 * metotlara opsiyonel olarak geçilebilir.
 *
 * @see \App\Repositories\Criteria\QueryParameters
 * @see \Illuminate\Database\Eloquent\SoftDeletes
 */
interface SoftDeletesRepositoryInterface
{
    /* ===================== Listeleme: Aktif + Silinmiş Birlikte ===================== */

    /**
     * Hem aktif hem de silinmiş (trashed) kayıtları, kriterlere göre birlikte getirir.
     * Parametresiz çağrıldığında tüm kayıtları getirir.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, limit, columns
     * @return Collection<Model>
     */
    public function withTrashed(?QueryParameters $criteria = null): Collection;

    /**
     * Hem aktif hem de silinmiş (trashed) kayıtları, sayfalı olarak getirir.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, columns
     * @param int                  $perPage
     * @param string               $pageName
     * @param int|null             $page
     * @return LengthAwarePaginator
     */
    public function withTrashedPaginate(
        ?QueryParameters $criteria = null,
        int $perPage = 15,
        string $pageName = 'page',
        ?int $page = null
    ): LengthAwarePaginator;

    /* ===================== Listeleme: Sadece Silinmiş ===================== */

    /**
     * Sadece silinmiş (çöp kutusundaki) kayıtları, kriterlere göre getirir.
     * Parametresiz çağrıldığında tüm silinmiş kayıtları getirir.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, limit, columns
     * @return Collection<Model>
     */
    public function onlyTrashed(?QueryParameters $criteria = null): Collection;

    /**
     * Sadece silinmiş (çöp kutusundaki) kayıtları, sayfalı olarak getirir.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, columns
     * @param int                  $perPage
     * @param string               $pageName
     * @param int|null             $page
     * @return LengthAwarePaginator
     */
    public function onlyTrashedPaginate(
        ?QueryParameters $criteria = null,
        int $perPage = 15,
        string $pageName = 'page',
        ?int $page = null
    ): LengthAwarePaginator;

    /* ===================== Bulma: Aktif + Silinmiş Birlikte ===================== */

    /**
     * Belirtilen kriterlere uyan İLK kaydı (silinmiş dahil) döndürür.
     * `orderBy` verilirse sıralanmış sonucun ilkini getirir.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, columns
     * @return Model|null
     */
    public function findWithTrashedBy(?QueryParameters $criteria = null): ?Model;

    /**
     * Belirtilen kriterlere uyan İLK kaydı (silinmiş dahil) döndürür; bulunamazsa exception atar.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, columns
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findWithTrashedByOrFail(?QueryParameters $criteria = null): Model;

    /**
     * ID ile tek kayıt döndürür (silinmiş dahil).
     *
     * @param int                $id
     * @param array<int,string>  $relations
     * @param array<int,string>  $columns
     * @return Model|null
     */
    public function findWithTrashedById(int $id, array $relations = [], array $columns = ['*']): ?Model;

    /**
     * ID ile tek kayıt döndürür (silinmiş dahil); bulunamazsa exception atar.
     *
     * @param int                $id
     * @param array<int,string>  $relations
     * @param array<int,string>  $columns
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findWithTrashedByIdOrFail(int $id, array $relations = [], array $columns = ['*']): Model;

    /* ===================== Bulma: Sadece Silinmiş ===================== */

    /**
     * Belirtilen kriterlere uyan İLK kaydı sadece silinmişler arasından döndürür.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, columns
     * @return Model|null
     */
    public function findOnlyTrashedBy(?QueryParameters $criteria = null): ?Model;

    /**
     * Belirtilen kriterlere uyan İLK kaydı sadece silinmişler arasından döndürür; bulunamazsa exception atar.
     *
     * @param QueryParameters|null $criteria  Kullanılabilecek alanlar: filters, relations, orderBy, columns
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOnlyTrashedByOrFail(?QueryParameters $criteria = null): Model;

    /**
     * ID ile tek kaydı sadece silinmişler arasından döndürür.
     *
     * @param int                $id
     * @param array<int,string>  $relations
     * @param array<int,string>  $columns
     * @return Model|null
     */
    public function findOnlyTrashedById(int $id, array $relations = [], array $columns = ['*']): ?Model;

    /**
     * ID ile tek kaydı sadece silinmişler arasından döndürür; bulunamazsa exception atar.
     *
     * @param int                $id
     * @param array<int,string>  $relations
     * @param array<int,string>  $columns
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOnlyTrashedByIdOrFail(int $id, array $relations = [], array $columns = ['*']): Model;

    /* ===================== İşlemler: Geri Yükleme & Kalıcı Silme ===================== */

    /**
     * Silinmiş (trashed) bir kaydı ID'sine göre geri yükler.
     *
     * @param int $id  Silinmiş kaydın ID'si.
     * @return bool    Başarı durumu (implementasyonda true/false normalize edilmelidir).
     * @throws ModelNotFoundException
     */
    public function restore(int $id): bool;

    /**
     * Bir kaydı ID'sine göre veritabanından kalıcı olarak siler (force delete).
     *
     * @param int $id  Kalıcı olarak silinecek kaydın ID'si.
     * @return bool    Başarı durumu (implementasyonda true/false normalize edilmelidir).
     * @throws ModelNotFoundException
     */
    public function forceDelete(int $id): bool;
}
