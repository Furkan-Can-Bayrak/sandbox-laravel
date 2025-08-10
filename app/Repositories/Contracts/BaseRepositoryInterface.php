<?php

namespace App\Repositories\Contracts;

use App\Repositories\Criteria\QueryParameters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Tüm repository'ler için temel sözleşme (contract).
 *
 * Bu arayüz, temel CRUD işlemlerini ve gelişmiş, kriter bazlı sorgulama
 * yeteneklerini tanımlar. Gelişmiş sorgular, QueryParameters nesnesi
 * aracılığıyla yapılır ve bu nesne, metotlara opsiyonel olarak geçilebilir.
 *
 * @see \App\Repositories\Criteria\QueryParameters
 */
interface BaseRepositoryInterface
{

    /**
     * Modele ait tüm kayıtları koşulsuz olarak getirir.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all(): Collection;

    /**
     * Kriterlere göre kayıtları döndürür.
     * Parametresiz çağrıldığında, varsayılan olarak tüm kayıtları getirir.
     *
     * @param QueryParameters|null $criteria QueryParameters nesnesinin şu özelliklerini kullanır:
     *                                       - `filters`
     *                                       - `relations`
     *                                       - `orderBy`
     *                                       - `limit`
     *                                       - `columns`
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(?QueryParameters $criteria = null): Collection;

    /**
     * Kriterlere göre sayfalı liste döndürür.
     *
     * @param QueryParameters|null $criteria QueryParameters nesnesinin şu özelliklerini kullanır:
     *                                       - `filters`
     *                                       - `relations`
     *                                       - `orderBy`
     *                                       - `columns`
     * @param int                  $perPage
     * @param string               $pageName
     * @param int|null             $page
     * @return LengthAwarePaginator
     */
    public function paginate(?QueryParameters $criteria = null, int $perPage = 15, string $pageName = 'page', ?int $page = null): LengthAwarePaginator;




    /**
     * Belirtilen kriterlere uyan İLK kaydı bulur.
     * `orderBy` kriteri belirtilirse, sıralanmış sonucun ilkini getirir (örn: en yeni ürün).
     * Belirtilmezse, filtreye uyan herhangi bir ilk kaydı getirir.
     *
     * @param QueryParameters|null $criteria QueryParameters nesnesinin şu özelliklerini kullanır:
     *                                       - `filters`
     *                                       - `relations`
     *                                       - `orderBy`
     *                                       - `columns`
     * @return Model|null
     */
    public function findBy(?QueryParameters $criteria = null): ?Model;

    /**
     * Belirtilen kriterlere uyan İLK kaydı bulur veya exception fırlatır.
     *
     * @param QueryParameters|null $criteria QueryParameters nesnesinin şu özelliklerini kullanır:
     *                                       - `filters`
     *                                       - `relations`
     *                                       - `orderBy`
     *                                       - `columns`
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByOrFail(?QueryParameters $criteria = null): Model;

    /**
     * ID ile tek kayıt döndürür. Bu, en hızlı ve direkt bulma yöntemidir.
     *
     * @param int                $id        Bulunacak kaydın birincil anahtarı.
     * @param array<int, string> $relations Birlikte yüklenecek ilişkiler. Örn: ['posts', 'profile']
     * @param array<int, string> $columns   Seçilecek kolonlar.
     * @return Model|null
     */
    public function findById(int $id, array $relations = [], array $columns = ['*']): ?Model;
    
    /**
     * ID ile tek kayıt döndürür veya exception fırlatır.
     *
     * @param int                $id
     * @param array<int, string> $relations
     * @param array<int, string> $columns
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id, array $relations = [], array $columns = ['*']): Model;

   
    
    /**
     * Veritabanına yeni bir kayıt oluşturur.
     *
     * @param array<string, mixed> $data Oluşturulacak verileri içeren dizi.
     * @return Model Oluşturulan yeni model örneği.
     */
    public function create(array $data): Model;
    
    /**
     * Mevcut bir kaydı ID'sine göre günceller.
     *
     * @param int                  $id   Güncellenecek kaydın ID'si.
     * @param array<string, mixed> $data Yeni verileri içeren dizi.
     * @return Model Güncellenmiş model örneği.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \RuntimeException
     */
    public function update(int $id, array $data): Model;
    
    /**
     * Bir kaydı ID'sine göre siler.
     *
     * @param int $id Silinecek kaydın ID'si.
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \RuntimeException
     */
    public function delete(int $id): void;
}